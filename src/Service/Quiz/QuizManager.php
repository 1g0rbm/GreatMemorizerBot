<?php

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;

class QuizManager
{
    /** @var QuizBuilder */
    private $quizBuilder;

    /** @var QuizRepository */
    private $quizRepository;

    public function __construct(QuizBuilder $quizBuilder, QuizRepository $quizRepository)
    {
        $this->quizBuilder    = $quizBuilder;
        $this->quizRepository = $quizRepository;
    }

    /**
     * @throws DBALException
     * @throws ORMException
     */
    public function getQuizByChat(Chat $chat): Quiz
    {
        $quiz = $this->quizRepository->findIncompleteQuizByChat($chat);

        return $quiz ?: $this->quizBuilder->build($chat);
    }
}