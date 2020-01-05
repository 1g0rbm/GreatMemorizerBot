<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Service\EntityFlusher;

class QuizBuilder
{
    private QuizRepository $quizRepository;

    private QuizStepBuilder $quizStepBuilder;

    private EntityFlusher $flusher;

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
    public function build(Chat $chat, ?int $wordListId = null): Quiz
    {
        $quiz = new Quiz();
        $quiz->setChat($chat);
        $quiz->setWordListId($wordListId);
        $quiz->setSteps($this->quizStepBuilder->buildForQuiz($quiz));
        $quiz->setCurrentStep($quiz->getSteps()->first());

        $this->quizRepository->addQuiz($quiz);
        $this->flusher->flush();

        return $quiz;
    }
}
