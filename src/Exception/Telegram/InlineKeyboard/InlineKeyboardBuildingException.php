<?php

namespace Ig0rbm\Memo\Exception\Telegram\InlineKeyboard;

use DomainException;

class InlineKeyboardBuildingException extends DomainException
{
    public static function becauseCollectionContainsNotOnlyInlineButtons(string $message): self
    {
        return new self($message, 500);
    }
}
