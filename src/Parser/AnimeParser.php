<?php

namespace CheckerKitsu\Parser;

use CheckerKitsu\Model\Anime;
use CheckerKitsu\Model\ExternalLink;

class AnimeParser
{
    public static function parse(object $response) : Anime
    {
        $model = new Anime();

        $model->setId($response->id);
        $model->setTitleEnglish($response->attributes->titles->en);
        $model->setTitleNative($response->attributes->titles->ja_jp);
        $model->setTitleRomaji($response->attributes->titles->en_jp);
        $model->setTitleCanonical($response->attributes->canonicalTitle);
        $model->setTitlesAbbreviated($response->attributes->abbreviatedTitles);
        $model->setEpisodes($response->attributes->episodeCount);
        $model->setStartDate($response->attributes->startDate);
        $model->setEndDate($response->attributes->endDate);
        $model->setType($response->attributes->showType);

        return $model;
    }
}