<?php

namespace Ig0rbm\Memo\Service\Quiz;

use Iterator;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;

class Rotator
{
    public function rotate(Quiz $quiz): ?QuizStep
    {
        /** @var Iterator $stepsIterator */
        $stepsIterator       = $quiz->getSteps()->getIterator();
        $findFirstUnanswered = false;
        while ($findFirstUnanswered === false && $stepsIterator->valid()) {
            /** @var QuizStep $step */
            $step = $stepsIterator->current();
            if ($step->isAnswered()) {
                $stepsIterator->next();
                continue;
            }

            $findFirstUnanswered = true;
        }

        if (isset($step)) {
            return $step->isAnswered() ? null : $step;
        }

        return null;
    }
}
