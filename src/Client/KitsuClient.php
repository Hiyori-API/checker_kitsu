<?php

namespace CheckerKitsu\Client;

use GuzzleHttp\Client;

class KitsuClient
{
    private $request;
    private $client;

    public function __construct($client = null)
    {
        $this->client = $client ?? new Client();
    }

    public function request(string $request, array $variables = []) {
        $request = sprintf("%s%s", $_ENV['KITSU_API_URL'], $request);
        $request = sprintf($request, ...$variables);

        echo "Fetching {$request}\n";

        return $this->client->get(
            $request
        );
    }
}