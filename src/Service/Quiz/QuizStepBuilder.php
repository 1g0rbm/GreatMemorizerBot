<?php

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\DBAL\DBALException;
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
    public function build(int $answersCount = 5): QuizStep
    {
        $words = $this->wordRepository->getRandomWords('en', $answersCount);
        $step  = new QuizStep();
        $step->setCorrectWord($words->first());
        $step->setWords($words);

        return $step;
    }
}
