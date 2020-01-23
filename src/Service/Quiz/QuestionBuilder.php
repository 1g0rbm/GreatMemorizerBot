<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\QuizStep;

use function sprintf;

final class QuestionBuilder
{
    public function build(QuizStep $step): string
    {
        return sprintf(
            'What is russian for "%s" and pos "%s"?',
            $step->getCorrectWord()->getText(),
            $step->getCorrectWord()->getPos()
        );
    }
}
