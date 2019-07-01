<?php

namespace Ig0rbm\Memo\Entity\Translation;

use Doctrine\Common\Collections\ArrayCollection;

class Word
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $langCode;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $pos;

    /**
     * @var string
     */
    private $transcription;

    /**
     * @var ArrayCollection|Word[]
     */
    private $translations;

    /**
     * @var ArrayCollection|Word[]
     */
    private $synonyms;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLangCode(): string
    {
        return $this->langCode;
    }

    /**
     * @param string $langCode
     */
    public function setLangCode(string $langCode): void
    {
        $this->langCode = $langCode;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getPos(): string
    {
        return $this->pos;
    }

    /**
     * @param string $pos
     */
    public function setPos(string $pos): void
    {
        $this->pos = $pos;
    }

    /**
     * @return string
     */
    public function getTranscription(): string
    {
        return $this->transcription;
    }

    /**
     * @param string $transcription
     */
    public function setTranscription(string $transcription): void
    {
        $this->transcription = $transcription;
    }

    /**
     * @return ArrayCollection|Word[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param ArrayCollection|Word[] $translations
     */
    public function setTranslations($translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return ArrayCollection|Word[]
     */
    public function getSynonyms()
    {
        return $this->synonyms;
    }

    /**
     * @param ArrayCollection|Word[] $synonyms
     */
    public function setSynonyms($synonyms): void
    {
        $this->synonyms = $synonyms;
    }
}