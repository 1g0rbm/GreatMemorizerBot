<?php

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class InlineKeyboard
{
    public const KEY_NAME = 'inline_keyboard';

    /** @var Collection */
    private $buttonsLines;

    public function __construct()
    {
        $this->buttonsLines = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getButtonsLines(): Collection
    {
        return $this->buttonsLines;
    }

    /**
     * @param Collection $buttonsLines
     */
    public function setButtonsLines(Collection $buttonsLines): void
    {
        $this->buttonsLines = $buttonsLines;
    }
}
