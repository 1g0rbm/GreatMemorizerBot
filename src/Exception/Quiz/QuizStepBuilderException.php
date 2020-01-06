<?php

namespace Ig0rbm\Memo\Exception\Quiz;

use RuntimeException;
use function sprintf;

class QuizStepBuilderException extends RuntimeException
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
