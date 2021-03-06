<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;

class Rotator
{
    public function rotate(Quiz $quiz): ?QuizStep
    {
        $step = $quiz->getSteps()
            ->filter(static fn(QuizStep $step) => $step->isAnswered() === false && $step)
            ->first();

        return $step ?: null;
    }
}
