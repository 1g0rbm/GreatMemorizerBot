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
use Psr\Cache\InvalidArgumentException;

class QuizBuilder
{
    private QuizRepository $quizRepository;

    private QuizStepBuilderByWordList $quizStepBuilderByWordList;

    private WordListRepository $wordListRepository;

    private EntityFlusher $flusher;

    public function __construct(
        QuizRepository $quizRepository,
        QuizStepBuilderByWordList $quizStepBuilderByWordList,
        WordListRepository $wordListRepository,
        EntityFlusher $flusher
    ) {
        $this->quizRepository            = $quizRepository;
        $this->quizStepBuilderByWordList = $quizStepBuilderByWordList;
        $this->wordListRepository        = $wordListRepository;
        $this->flusher                   = $flusher;
    }

    /**
     * @throws DBALException
     * @throws ORMException
     * @throws InvalidArgumentException
     */
    public function build(Chat $chat, bool $withWordList = false): Quiz
    {
        $quiz = new Quiz();
        $quiz->setChat($chat);

        if ($withWordList) {
            $wordList = $this->wordListRepository->getOneByChat($chat);
            $quiz->setWordListId($wordList->getId());
            $quiz->setWordList($wordList);
            $quiz->setType(Quiz::FROM_WORD_LIST);
        }

        $quiz->setSteps($this->quizStepBuilderByWordList->do($quiz));
        $quiz->setCurrentStep($quiz->getSteps()->first());

        $this->quizRepository->addQuiz($quiz);
        $this->flusher->flush();

        return $quiz;
    }
}
