<?php

namespace Ig0rbm\Memo\Exception\Quiz;

use RuntimeException;

class ResultantException extends RuntimeException
{
    public static function becauseQuizIsNotComplete(int $quizId): self
    {
        throw new self(sprintf('Quiz with id %d is not complete yet', $quizId));
    }
}
