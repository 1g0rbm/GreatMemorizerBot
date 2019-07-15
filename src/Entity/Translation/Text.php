<?php

namespace Ig0rbm\Memo\Entity\Translation;

class Text
{
    /**
     * @var integer
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
}