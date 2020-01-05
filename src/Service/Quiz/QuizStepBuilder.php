<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Exception\Quiz\QuizStepBuilderException;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Repository\Translation\WordRepository;

class QuizStepBuilder
{
    private WordRepository $wordRepository;

    private WordListRepository $wordListRepository;

    public function __construct(WordRepository $wordRepository, WordListRepository $wordListRepository)
    {
        $this->wordRepository     = $wordRepository;
        $this->wordListRepository = $wordListRepository;
    }

    /**
     * @throws DBALException
     */
    public function buildForQuiz(Quiz $quiz): ArrayCollection
    {
        if ($quiz->getWordListId()) {
            $this->wordListRepository->getOneById($quiz->getWordListId());
        }

        $step       = new QuizStep();
        $collection = new ArrayCollection();
        $wordsCount = $step->getLength() * $quiz->getLength();
        $words      = $this->wordRepository->getRandomWords(
            'en',
            'noun',
            $wordsCount
        );

        if ($words->count() % $step->getLength() !== 0) {
            throw QuizStepBuilderException::becauseThereAreWrongCountOfWordsFoundInDB($wordsCount, $words->count());
        }

        $stepAnswerCounter = 1;
        foreach ($words as $word) {
            $step = isset($step) && $stepAnswerCounter === 1 ? new QuizStep() : $step;

            if ($stepAnswerCounter === 1) {
                $step->setCorrectWord($word);
                $step->setQuiz($quiz);
            }

            $step->getWords()->add($word);

            if ($stepAnswerCounter === $step->getLength()) {
                $collection->add($step);
            }

            $stepAnswerCounter = $step->getLength() === $stepAnswerCounter ? 1 : $stepAnswerCounter + 1;
        }

        return $collection;
    }
}
