<?php

namespace Ig0rbm\Memo\Entity\Translation;

class Word
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $pos;

    /**
     * @var Translation[]
     */
    private $ts;

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
     * @return Translation[]
     */
    public function getTs(): array
    {
        return $this->ts;
    }

    /**
     * @param Translation[] $ts
     */
    public function setTs(array $ts): void
    {
        $this->ts = $ts;
    }
}