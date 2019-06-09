<?php

namespace Ig0rbm\Memo\Exception\Telegram\Message;

use DomainException;

class ParseMessageException extends DomainException
{
    public static function becauseInvalidParameter(string $message): self
    {
        return new self($message, 400);
    }
}