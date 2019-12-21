<?php

namespace Ig0rbm\Memo\Entity\Telegram\Keyboard;

class ReplyKeyboardRemove
{
    public const KEY_NAME = 'remove_keyboard';

    private bool $removeKeyboard = true;

    private bool $selective = false;

    public function isRemoveKeyboard(): bool
    {
        return $this->removeKeyboard;
    }

    public function setRemoveKeyboard(bool $removeKeyboard): void
    {
        $this->removeKeyboard = $removeKeyboard;
    }

    public function isSelective(): bool
    {
        return $this->selective;
    }

    public function setSelective(bool $selective): void
    {
        $this->selective = $selective;
    }
}
