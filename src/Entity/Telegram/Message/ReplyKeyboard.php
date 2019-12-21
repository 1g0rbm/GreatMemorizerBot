<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ReplyKeyboard
{
    public const KEY_NAME = 'reply_keyboard';

    private Collection $buttonsLines;

    public function __construct()
    {
        $this->buttonsLines = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|Collection|ReplyButton[]
     */
    public function getButtonsLines()
    {
        return $this->buttonsLines;
    }

    /**
     * @param ArrayCollection|Collection|ReplyButton[] $buttonsLines
     */
    public function setButtonsLines($buttonsLines): void
    {
        $this->buttonsLines = $buttonsLines;
    }
}
