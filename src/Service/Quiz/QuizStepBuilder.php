<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Exception\Quiz\QuizStepBuilderException;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use function intdiv;

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
    public function buildForQuiz(Quiz $quiz): ArrayCollection
    {
        $step       = new QuizStep();
        $collection = new ArrayCollection();
        $wordsCount = $step->getLength() * $quiz->getLength();

        if ($quiz->getWordListId()) {
            $words = $this->wordRepository->getRandomWordsByWordListId(
                'en',
                'noun',
                $quiz->getWordListId(),
                $step->getLength() * $quiz->getLength()
            );
        } else {
            $words = $this->wordRepository->getRandomWords(
                'en',
                'noun',
                $wordsCount
            );
        }

        $count = intdiv($words->count(), $step->getLength());
        if ($count === 0) {
            throw QuizStepBuilderException::becauseThereAreNotEnoughWords();
        }

        $quiz->setLength($count);

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

            if ($collection->count() === $count) {
                break;
            }

            $stepAnswerCounter = $step->getLength() === $stepAnswerCounter ? 1 : $stepAnswerCounter + 1;
        }

        return $collection;
    }
}
