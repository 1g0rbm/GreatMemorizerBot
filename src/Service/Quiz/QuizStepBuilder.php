<?php

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Repository\Translation\WordRepository;

class QuizStepBuilder
{
    /** @var WordRepository */
    private $wordRepository;

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
        $words      = $this->wordRepository->getRandomWords(
            'en',
            $answersCount * $quiz->getLength()
        );

        foreach ($words as $word) {
            $step = isset($step) && $step->getWords()->count() === $answersCount ? new QuizStep() : $step;

            if ($step->getWords()->count() === 0) {
                $step->setCorrectWord($word);
            }

            $step->getWords()->add($word);

            if ($step->getWords()->count() === $answersCount) {
                $collection->add($step);
            }
        }

        return $collection;
    }
}
