<?php

namespace CheckerKitsu;

use CheckerKitsu\Client\KitsuClient;
use CheckerKitsu\Model\Anime;
use CheckerKitsu\Parser\AnimeParser;
use CheckerKitsu\Parser\ExternalLinkParser;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Kitsu
{

    private $parsingQueue = [];
    private $unreferencedQueue = [];
    private $hiyoriDb = [];

    private $offset;
    private $count;


    // Dump Data to mongodb after self::autosave entries are added to $parsingQueue
    public  $autosave = 100;


    private $dotenv;
    private $kitsu;
    private $guzzle;
    private $mongodb;
    private $query;
    private $log;

    /**
     * @var Anime[]
     */
    private $database;

    /**
     * @var ResponseInterface
     */
    private $response;
    /**
     * @var Object
     */
    private $responseBody;
    /**
     * @var int
     */
    private $requestStatusCode;

    /**
     * @var int
     *
     * Seconds we need to wait until we can make another accepted request
     */
    private $rateLimitRetryAfter;
    private $sleeping = false;

    public function __construct()
    {
        $this->dotenv = new Dotenv();
        $this->dotenv->load(__DIR__.'/../.env');

        $this->log = new Logger('checker');
        $this->log->pushHandler(new StreamHandler(__DIR__.'/event.log', Logger::DEBUG));

        $this->guzzle = new Client([
            'http_errors' => false
        ]);
        $this->kitsu = new KitsuClient($this->guzzle);
        $this->mongodb = new \MongoDB\Client(
            sprintf(
                'mongodb+srv://%s:%s@%s',
                $_ENV['MONGODB_USERNAME'],
                $_ENV['MONGODB_PASSWORD'],
                $_ENV['MONGODB_CONNECTION_STRING']
            )
        );

        $this->offset = 0;
        $this->count = 1;
        $this->autosave = $_ENV['AUTOSAVE'];

        $this->rateLimitRetryAfter=$_ENV['RATE_LIMIT_RETRY_AFTER'];


        $this->query = "anime?page[limit]=20&page[offset]=%d";
    }

    public function start()
    {
        while ($this->offset <= $this->count) {

            if ($this->sleeping) {
                $this->log->debug("Waking up...");
                echo "Waking up...\n";
                $this->sleeping = false;
            }

            $this->log->debug("Fetching anime {$this->offset}/{$this->count}");
            echo "Fetching anime {$this->offset}/{$this->count}\n";

            $this->response = $this->kitsu->request($this->query, [$this->offset]);
            $this->requestStatusCode = $this->response->getStatusCode();

            $this->log->debug("Response [{$this->requestStatusCode}];");
            echo "Response [{$this->requestStatusCode}]\n";

            if ($this->requestStatusCode === 429) {

                $this->log->error("{$this->requestStatusCode} Rate Limited. Going to sleep for {$this->rateLimitRetryAfter} seconds");
                echo "{$this->requestStatusCode} Rate Limited. Going to sleep for {$this->rateLimitRetryAfter} seconds\n";
                $this->sleeping = true;
                sleep((int) $this->rateLimitRetryAfter+2);
                continue;
            }

            $this->responseBody = json_decode(
                (string) $this->response->getBody()
            );

            $this->count = $this->responseBody->meta->count;
            $node = $this->responseBody->data;

            foreach ($node as $nodeItem) {
                // todo calculate end reach probability if provided with required flag

                $model = AnimeParser::parse($nodeItem);

                $this->log->debug("Fetching external links ID:{$model->getId()}");
                echo "Fetching external links ID:{$model->getId()}\n";

                $response = $this->kitsu->request('anime/%d/mappings', [$model->getId()]);
                $requestStatusCode = $response->getStatusCode();

                $this->log->debug("Response [{$requestStatusCode}];");
                echo "Response [{$requestStatusCode}];\n";

                if ($requestStatusCode === 429) {

                    $this->log->error("{$requestStatusCode} Rate Limited. Going to sleep for {$this->rateLimitRetryAfter} seconds");
                    echo "{$requestStatusCode} Rate Limited. Going to sleep for {$this->rateLimitRetryAfter} seconds\n";
                    sleep((int) $this->rateLimitRetryAfter+2);
                }

                $responseBody = json_decode(
                    (string) $response->getBody()
                );

                $externalLinks = ExternalLinkParser::parse(
                    $responseBody
                );

                $model->setExternalLinks($externalLinks);

                $this->database[] = $model;
                sleep($_ENV['SLEEP_BETWEEN_REQUESTS']);
            }

            if (count($this->database) >= $this->autosave) {
                $this->push();

                $this->database = [];
                $this->endReachProbabilityCount = 0;
            }

            $this->offset += 20;
        }
    }

    private function exists($id)
    {
        $found = $this->mongodb->hiyori->kitsu_processing_queue->findOne([
            'kitsu_id' => $id
        ]);

        return $found === null ? false : true;
    }

    private function push()
    {
        $count = count($this->database);
        echo "Pushing {$count} document(s) to Database\n";
        $this->log->info("Pushing {$count} document(s) to Database");

        $dump = [];

        foreach ($this->database as $anime) {

            // Check if exists in MongoDB
            if ($this->exists($anime->getId())) {

                $this->log->info("Skipping {$anime->getId()}; already in database");
                echo "Skipping {$anime->getId()}; already in database\n";

                continue;
            }

            $externalLinks = [];
            if (is_iterable($anime->getExternalLinks())) {
                foreach ($anime->getExternalLinks() as $link) {
                    $externalLinks[] = [
                        'id' => $link->getId(),
                        'site' => $link->getSite(),
                    ];
                }
            }

            $dump[] = [
                'kitsu_id' => $anime->getId(),
                'title_english' => $anime->getTitleEnglish(),
                'title_native' => $anime->getTitleNative(),
                'title_romaji' => $anime->getTitleRomaji(),
                'title_canonical' => $anime->getTitleCanonical(),
                'titles_abbreviated' => $anime->getTitlesAbbreviated(),
                'episodes' => $anime->getEpisodes(),
                'start_date' => $anime->getStartDate(),
                'end_date' => $anime->getEndDate(),
                'type' => $anime->getType(),
                'external_links' => $externalLinks,
            ];
        }

        try {
            $result = $this->mongodb->hiyori->kitsu_processing_queue->insertMany($dump);

            echo "Inserted {$result->getInsertedCount()} document(s)\n";
            $this->log->info("Inserted {$result->getInsertedCount()} document(s)");

        } catch (\Exception $e) {
            $this->log->error("Failed to push to Database. Dumping...");
            echo "Failed to push to Database. Dumping...\n";

            file_put_contents(
                time().'.json',
                json_encode($dump)
            );
        }
    }
}
