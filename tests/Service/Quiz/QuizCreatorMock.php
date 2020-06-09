<?php

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Service\Quiz\Creator\QuizCreatorInterface;

class QuizCreatorMock implements QuizCreatorInterface
{
    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function create(Chat $chat): Quiz
    {
        $quiz = new Quiz();
        $quiz->setChat($chat);
        $quiz->setType($this->type());

        return $quiz;
    }
}