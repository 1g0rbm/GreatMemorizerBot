<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Quiz;

use RuntimeException;

use function sprintf;

class QuizStepBuilderException extends RuntimeException implements QuizExceptionInterface
{
    public static function becauseThereAreWrongCountOfWordsFoundInDB(int $needed, int $founded): self
    {
        return new self(sprintf('Need words for quiz %d, but found %d', $needed, $founded), 500);
    }

    public static function becauseThereAreNotEnoughWords(): self
    {
        return new self('Not enough words for build quiz');
    }
}
