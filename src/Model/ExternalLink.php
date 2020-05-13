<?php

namespace CheckerKitsu\Model;


/**
 * Class ExternalLink
 * @package CheckerKitsu\Model
 */
class ExternalLink
{
    /**
     * @var int|string
     */
    private $id;
    /**
     * @var string
     */
    private $site;
    /**
     * @var string
     */
    private $url;

    /**
     * @param string $id
     * @param string $site
     * @return ExternalLink
     */
    public static function new(string $id, string $site) : self
    {
        $instance = new self();

        $instance->id = $id;
        $instance->site = $site;

        return $instance;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}