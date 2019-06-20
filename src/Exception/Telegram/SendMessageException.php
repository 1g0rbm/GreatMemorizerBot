<?php

namespace Ig0rbm\Memo\Exception\Telegram;

use RuntimeException;

class SendMessageException extends RuntimeException
{
    public static function becauseTransportError(string $message): self
    {
        return new self($message, 500);
    }
}