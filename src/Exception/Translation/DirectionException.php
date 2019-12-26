<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Translation;

use DomainException;

use function sprintf;

class DirectionException extends DomainException
{
    public static function becauseDefaultDirectionNotFound(): self
    {
        return new self('There is no default direction id DB', 500);
    }

    public static function becauseDirectionNotFound(int $directionId): self
    {
        return new self(sprintf('There is not direction with id %d', $directionId));
    }
}
