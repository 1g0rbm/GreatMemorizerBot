<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Psr\Cache\InvalidArgumentException;

class QuizManager
{
    private QuizBuilder $quizBuilder;

    private QuizRepository $quizRepository;

    public function __construct(QuizBuilder $quizBuilder, QuizRepository $quizRepository)
    {
        $this->quizBuilder    = $quizBuilder;
        $this->quizRepository = $quizRepository;
    }

    /**
     * @throws DBALException
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function getQuizByChat(Chat $chat, bool $withWordList = false): Quiz
    {
        $quiz = $this->quizRepository->findIncompleteQuizByChat($chat);

        return $quiz ?: $this->quizBuilder->build($chat, $withWordList);
    }
}