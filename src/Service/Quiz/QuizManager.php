<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Registry\QuizCreatorRegistry;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;

class QuizManager
{
    private QuizRepository $quizRepository;

    private QuizCreatorRegistry $registry;

    public function __construct(QuizRepository $quizRepository, QuizCreatorRegistry $registry)
    {
        $this->quizRepository = $quizRepository;
        $this->registry       = $registry;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function get(Chat $chat, string $type): Quiz
    {
        $quiz = $this->quizRepository->findIncompleteByChatAndType($chat, $type);
        if ($quiz) {
            return $quiz;
        }

        return $this->registry->getQuizCreator($type)->create($chat);
    }
}
