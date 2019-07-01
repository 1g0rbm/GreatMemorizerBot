<?php


namespace Ig0rbm\Memo\Entity\Translation;


class Direction
{
    /**
     * @var string
     */
    private $langFrom;

    /**
     * @var string
     */
    private $langTo;

    /**
     * @var string
     */
    private $direction;

    /**
     * @return string
     */
    public function getLangFrom(): string
    {
        return $this->langFrom;
    }

    /**
     * @param string $langFrom
     */
    public function setLangFrom(string $langFrom): void
    {
        $this->langFrom = $langFrom;
    }

    /**
     * @return string
     */
    public function getLangTo(): string
    {
        return $this->langTo;
    }

    /**
     * @param string $langTo
     */
    public function setLangTo(string $langTo): void
    {
        $this->langTo = $langTo;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     */
    public function setDirection(string $direction): void
    {
        $this->direction = $direction;
    }
}