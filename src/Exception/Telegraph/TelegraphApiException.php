<?php

namespace Ig0rbm\Memo\Exception\Telegraph;

use RuntimeException;

class TelegraphApiException extends RuntimeException
{
    public static function becauseBadRequestToTelegraph(string $message): self
    {
        return new self($message, 500);
    }

    public static function becauseBadResponseFromTelegraph(string $message): self
    {
        return new self($message, 500);
    }
}
