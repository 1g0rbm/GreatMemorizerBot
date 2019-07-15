<?php

namespace Ig0rbm\Memo\Exception\Translation\Yandex;

use Exception;

class TranslationException extends Exception
{
    public static function becauseBadRequestFromYandexApi(string $message): self
    {
        return new self($message, 500);
    }
}