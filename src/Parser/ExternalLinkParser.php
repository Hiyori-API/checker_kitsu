<?php

namespace CheckerKitsu\Parser;

use CheckerKitsu\Model\Anime;
use CheckerKitsu\Model\ExternalLink;

class ExternalLinkParser
{
    public static function parse(object $response) : array
    {
        $links = [];

        if (!empty($response->data)) {

            foreach ($response->data as $link) {
                $links[] = ExternalLink::new(
                    $link->attributes->externalId,
                    $link->attributes->externalSite
                );
            }
        }

        return $links;
    }
}