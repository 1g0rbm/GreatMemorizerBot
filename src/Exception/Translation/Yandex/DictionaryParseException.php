<?php

namespace Ig0rbm\Memo\Exception\Translation\Yandex;

use Exception;

class DictionaryParseException extends Exception
{
    public static function becauseFieldNotFound(string $fieldName): self
    {
        return new self(sprintf('Field with name "%s" was not found', $fieldName), 500);
    }
}