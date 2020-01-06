<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Quiz;

use RuntimeException;

class QuizException extends RuntimeException implements QuizExceptionInterface
{
    public static function becauseThereIsNoQuizForChat(int $chatId): self
    {
        return new self(sprintf('There is not unanswered quiz for chat with id %d', $chatId));
    }
}
