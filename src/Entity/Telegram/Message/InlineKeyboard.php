<?php

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class InlineKeyboard
{
    public const KEY_NAME = 'inline_keyboard';

    private Collection $buttonsLines;

    public function __construct()
    {
        $this->buttonsLines = new ArrayCollection();
    }

    /**
     * @return Collection|InlineButton[]
     */
    public function getButtonsLines(): Collection
    {
        return $this->buttonsLines;
    }

    /**
     * @param Collection|InlineButtonInterface[] $buttonsLines
     */
    public function setButtonsLines(Collection $buttonsLines): void
    {
        $this->buttonsLines = $buttonsLines;
    }
}
