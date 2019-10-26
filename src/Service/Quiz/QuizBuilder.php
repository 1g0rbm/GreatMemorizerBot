<?php

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Service\EntityFlusher;

class QuizBuilder
{
    /** @var QuizRepository */
    private $quizRepository;

    /** @var QuizStepBuilder */
    private $quizStepBuilder;

    /** @var EntityFlusher */
    private $flusher;

    public function __construct(
        QuizRepository $quizRepository,
        QuizStepBuilder $quizStepBuilder,
        EntityFlusher $flusher
    ) {
        $this->quizRepository  = $quizRepository;
        $this->quizStepBuilder = $quizStepBuilder;
        $this->flusher         = $flusher;
    }

    /**
     * @throws DBALException
     * @throws ORMException
     */
    public function build(Chat $chat, int $answersCount = 4, int $stepsCount = 5): Quiz
    {
        $quiz = new Quiz();
        $quiz->setChat($chat);
        $quiz->setLength($stepsCount);
        $quiz->setSteps($this->quizStepBuilder->buildForQuiz($quiz, $answersCount));

        $this->quizRepository->addQuiz($quiz);
        $this->flusher->flush();

        return $quiz;
    }
}
