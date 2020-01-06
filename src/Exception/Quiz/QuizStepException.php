<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Quiz;

use RuntimeException;

class QuizStepException extends RuntimeException implements QuizExceptionInterface
{
    public static function becauseThereAreNotQuizSteps(int $quizId): self
    {
        return new self(sprintf('There are not quiz steps in quiz with id %d ', $quizId));
    }

    public static function becauseThereAreNotUnansweredSteps(int $quizId): self
    {
        return new self(sprintf('There are not unanswered steps in quiz with id %d ', $quizId));
    }
}
