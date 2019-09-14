<?php

namespace Ig0rbm\Memo\Exception\Translation;

use DomainException;

class DirectionException extends DomainException
{
    public static function becauseDefaultDirectionNotFound(): self
    {
        return new self('There is no default direction id DB', 500);
    }
}
