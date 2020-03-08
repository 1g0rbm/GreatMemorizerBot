<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\Quiz\Rotator;
use PHPUnit\Framework\TestCase;

class RotatorUnitTest extends TestCase
{
    private Rotator $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new Rotator();
    }

    public function testRotateReturnNextStep(): void
    {
        $answer1 = 'answer1';
        $answer2 = 'answer2';
        $answer3 = 'answer3';
        $quiz    = $this->getQuiz();

        $quiz->getSteps()->add($this->getStep($answer1, true));
        $quiz->getSteps()->add($this->getStep($answer2, false));
        $quiz->getSteps()->add($this->getStep($answer3, false));

        $step = $this->service->rotate($quiz);

        $this->assertEquals($answer2, $step->getCorrectWord()->getText());
    }

    public function testRotateReturnNull(): void
    {
        $answer1 = 'answer1';
        $quiz    = $this->getQuiz();

        $quiz->getSteps()->add($this->getStep($answer1, true));

        $this->assertNull($this->service->rotate($quiz));
    }

    private function getWord(string $text): Word
    {
        $translation = new Word();
        $translation->setText($text);

        $word = new Word();
        $word->setText($text);
        $word->getTranslations()->add($translation);

        return $word;
    }

    private function getStep(string $answer, bool $isAnswered): QuizStep
    {
        $step = new QuizStep($this->getQuiz());
        $step->setIsAnswered($isAnswered);
        $step->setCorrectWord($this->getWord($answer));

        return $step;
    }

    private function getQuiz(): Quiz
    {
        $quiz = new Quiz();
        $quiz->setId(22);

        return $quiz;
    }
}
