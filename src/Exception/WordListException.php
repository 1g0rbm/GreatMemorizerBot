<?php

namespace Ig0rbm\Memo\Exception;

use DomainException;

class WordListException extends DomainException
{
    public static function becauseListAlreadyHasSameWord(): self
    {
        return new self('List already has the same word');
    }
}
