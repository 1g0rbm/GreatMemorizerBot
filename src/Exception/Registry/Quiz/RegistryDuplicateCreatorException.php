<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Registry\Quiz;

use RuntimeException;

class RegistryDuplicateCreatorException extends RuntimeException
{
    public static function byType(string $type): self
    {
        return new self(sprintf('Container already has the same creator for type "%s"', $type));
    }
}
