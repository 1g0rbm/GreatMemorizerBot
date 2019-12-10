<?php

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Exception\Quiz\QuizStepBuilderException;
use Ig0rbm\Memo\Repository\Translation\WordRepository;

class QuizStepBuilder
{
    private WordRepository $wordRepository;

    public function __construct(WordRepository $wordRepository)
    {
        $this->wordRepository = $wordRepository;
    }

    /**
     * @throws DBALException
     */
    public function buildForQuiz(Quiz $quiz, int $answersCount): ArrayCollection
    {
        $step       = new QuizStep();
        $collection = new ArrayCollection();
        $wordsCount = $answersCount * $quiz->getLength();
        $words      = $this->wordRepository->getRandomWords(
            'en',
            'noun',
            $wordsCount
        );

        if ($words->count() % $answersCount !== 0) {
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

            if ($stepAnswerCounter === $answersCount) {
                $collection->add($step);
            }

            $stepAnswerCounter = $answersCount === $stepAnswerCounter ? 1 : $stepAnswerCounter + 1;
        }

        return $collection;
    }
}
