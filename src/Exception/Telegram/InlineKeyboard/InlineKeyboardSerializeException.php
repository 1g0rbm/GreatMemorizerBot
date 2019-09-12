<?php

namespace Ig0rbm\Memo\Exception\Telegram\InlineKeyboard;

use RuntimeException;

class InlineKeyboardSerializeException extends RuntimeException
{
    public static function becauseThereAreNoButtonInLine(int $line): self
    {
        return new self(
            sprintf('Keyboard serialize error on line %d. There are no buttons', $line),
            500
        );
    }
}
