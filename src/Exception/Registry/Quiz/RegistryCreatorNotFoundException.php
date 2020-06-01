<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Registry\Quiz;

use RuntimeException;

class RegistryCreatorNotFoundException extends RuntimeException
{
    public static function byType(string $type): self
    {
        return new self(sprintf('There is no quiz creator with type "%s"', $type));
    }
}
