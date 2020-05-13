<?php

namespace CheckerKitsu\Model;

/**
 * Class Anime
 * @package CheckerKitsu\Model
 */
class Anime
{
    /**
     * @var int
     *
     * Kitsu's ID
     */
    private $id;

    /**
     * @var null|string
     */
    private $titleEnglish;
    /**
     * @var null|string
     */
    private $titleRomaji;
    /**
     * @var null|string
     */
    private $titleNative;

    /**
     * @var null|string
     */
    private $titleCanonical;
    /**
     * @var array
     */
    private $titlesAbbreviated;

    /**
     * @var null|int
     */
    private $episodes;
    /**
     * @var null|string
     */
    private $startDate;
    /**
     * @var null|string
     */
    private $endDate;
    /**
     * @var null|string
     *
     * e.g "TV", "OVA", "Movie", etc
     */
    private $type;

    /**
     * @var array
     */
    private $externalLinks;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Anime
     */
    public function setId(int $id): Anime
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleEnglish(): ?string
    {
        return $this->titleEnglish;
    }

    /**
     * @param string|null $titleEnglish
     * @return Anime
     */
    public function setTitleEnglish(?string $titleEnglish): Anime
    {
        $this->titleEnglish = $titleEnglish;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleRomaji(): ?string
    {
        return $this->titleRomaji;
    }

    /**
     * @param string|null $titleRomaji
     * @return Anime
     */
    public function setTitleRomaji(?string $titleRomaji): Anime
    {
        $this->titleRomaji = $titleRomaji;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleNative(): ?string
    {
        return $this->titleNative;
    }

    /**
     * @param string|null $titleNative
     * @return Anime
     */
    public function setTitleNative(?string $titleNative): Anime
    {
        $this->titleNative = $titleNative;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleCanonical(): ?string
    {
        return $this->titleCanonical;
    }

    /**
     * @param string|null $titleCanonical
     * @return Anime
     */
    public function setTitleCanonical(?string $titleCanonical): Anime
    {
        $this->titleCanonical = $titleCanonical;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getTitlesAbbreviated(): ?array
    {
        return $this->titlesAbbreviated;
    }

    /**
     * @param null|array $titlesAbbreviated
     * @return Anime
     */
    public function setTitlesAbbreviated(?array $titlesAbbreviated): Anime
    {
        $this->titlesAbbreviated = $titlesAbbreviated;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getEpisodes(): ?int
    {
        return $this->episodes;
    }

    /**
     * @param int|null $episodes
     * @return Anime
     */
    public function setEpisodes(?int $episodes): Anime
    {
        $this->episodes = $episodes;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    /**
     * @param string|null $startDate
     * @return Anime
     */
    public function setStartDate(?string $startDate): Anime
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    /**
     * @param string|null $endDate
     * @return Anime
     */
    public function setEndDate(?string $endDate): Anime
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return Anime
     */
    public function setType(?string $type): Anime
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return array
     */
    public function getExternalLinks(): array
    {
        return $this->externalLinks;
    }

    /**
     * @param array $externalLinks
     * @return Anime
     */
    public function setExternalLinks(array $externalLinks): Anime
    {
        $this->externalLinks = $externalLinks;
        return $this;
    }
}