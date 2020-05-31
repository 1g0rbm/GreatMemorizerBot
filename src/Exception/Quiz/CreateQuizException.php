<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Quiz;

use RuntimeException;

use function sprintf;

class CreateQuizException extends RuntimeException
{
    public static function wrongType(string $type): self
    {
        return new self(sprintf('Impossible to create quiz with type "%s"', $type));
    }
}
