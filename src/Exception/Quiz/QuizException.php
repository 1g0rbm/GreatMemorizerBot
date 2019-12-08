<?php

namespace Ig0rbm\Memo\Exception\Quiz;

use RuntimeException;

class QuizException extends RuntimeException
{
    public static function becauseThereIsNoQuizForChat(int $chatId): self
    {
        return new self(sprintf('There is not quiz for chat with id %d', $chatId));
    }
}
