<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\EntityFlusher;

class QuizBuilder
{
    private QuizRepository $quizRepository;

    private QuizStepBuilder $quizStepBuilder;

    private WordListRepository $wordListRepository;

    private EntityFlusher $flusher;

    public function __construct(
        QuizRepository $quizRepository,
        QuizStepBuilder $quizStepBuilder,
        WordListRepository $wordListRepository,
        EntityFlusher $flusher
    ) {
        $this->quizRepository     = $quizRepository;
        $this->quizStepBuilder    = $quizStepBuilder;
        $this->wordListRepository = $wordListRepository;
        $this->flusher            = $flusher;
    }

    /**
     * @throws DBALException
     * @throws ORMException
     */
    public function build(Chat $chat, bool $withWordList = false): Quiz
    {
        $quiz = new Quiz();
        $quiz->setChat($chat);

        if ($withWordList) {
            $wordList = $this->wordListRepository->getOneByChat($chat);
            $quiz->setWordListId($wordList->getId());
            $quiz->setWordList($wordList);
        }

        $quiz->setSteps($this->quizStepBuilder->buildForQuiz($quiz));
        $quiz->setCurrentStep($quiz->getSteps()->first());

        $this->quizRepository->addQuiz($quiz);
        $this->flusher->flush();

        return $quiz;
    }
}
