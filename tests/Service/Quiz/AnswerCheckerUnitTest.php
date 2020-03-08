<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Exception\Quiz\QuizException;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Quiz\AnswerChecker;
use Ig0rbm\Memo\Service\Quiz\Rotator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class AnswerCheckerUnitTest extends TestCase
{
    /** @var AnswerChecker */
    private $service;

    /** @var QuizRepository|MockObject */
    private $quizRepository;

    /** @var EntityFlusher|MockObject */
    private $flusher;

    public function setUp(): void
    {
        parent::setUp();

        $this->quizRepository = $this->createMock(QuizRepository::class);
        $this->flusher        = $this->createMock(EntityFlusher::class);

        $this->service = new AnswerChecker($this->quizRepository, new Rotator(), $this->flusher);
    }

    /**
     * @throws NonUniqueResultException
     * @throws QuizStepException
     */
    public function testCheckThrowExceptionIfThereIsNotQuiz(): void
    {
        $answer = 'answer';
        $chat   = $this->getChat();

        $this->quizRepository->expects($this->once())
            ->method('getIncompleteQuizByChat')
            ->with($chat)
            ->willThrowException(QuizException::becauseThereIsNoQuizForChat($chat->getId()));

        $this->flusher->expects($this->never())
            ->method('flush');

        $this->expectException(QuizException::class);

        $this->service->check($chat, $answer);
    }

    /**
     * @throws NonUniqueResultException
     * @throws QuizStepException
     */
    public function testThrowExceptionIfThereAreNotUnansweredQuizSteps(): void
    {
        $answer = 'answer';
        $chat   = $this->getChat();
        $quiz   = $this->getQuiz();
        $quiz->getSteps()->add($this->getStep($answer, true, $quiz));
        $quiz->setCurrentStep($quiz->getSteps()->first());

        $this->quizRepository->expects($this->once())
            ->method('getIncompleteQuizByChat')
            ->with($chat)
            ->willReturn($quiz);

        $this->flusher->expects($this->never())
            ->method('flush');

        $this->expectException(QuizStepException::class);

        $this->service->check($chat, $answer);
    }

    /**
     * @throws NonUniqueResultException
     * @throws QuizStepException
     */
    public function testCheckAndSwipeReturnStepIfThereIsAnotherStep(): void
    {
        $answer1 = 'answer1';
        $answer2 = 'answer2';
        $answer3 = 'answer3';
        $chat    = $this->getChat();
        $quiz    = $this->getQuiz();

        $step2 = $this->getStep($answer2, false, $quiz);
        $quiz->getSteps()->add($this->getStep($answer1, true, $quiz));
        $quiz->getSteps()->add($step2);
        $quiz->getSteps()->add($this->getStep($answer3, false, $quiz));

        $quiz->setCurrentStep($this->getStep($answer2, false, $quiz));

        $this->quizRepository->expects($this->once())
            ->method('getIncompleteQuizByChat')
            ->with($chat)
            ->willReturn($quiz);

        $this->flusher->expects($this->once())
            ->method('flush');

        $quiz = $this->service->check($chat, $answer2);
        $step = $quiz->getCurrentStep();

        /** @var Word $word */
        $word = $step->getCorrectWord()->getTranslations()->first();

        $this->assertEquals($answer2, $word->getText());
        $this->assertFalse($quiz->isComplete());
    }

    /**
     * @throws NonUniqueResultException
     * @throws QuizStepException
     */
    public function testCheckAndSwipeReturnNullIfThereIsNotAnotherStep(): void
    {
        $answer1 = 'answer1';
        $answer2 = 'answer2';
        $answer3 = 'answer3';
        $chat    = $this->getChat();
        $quiz    = $this->getQuiz();

        $quiz->getSteps()->add($this->getStep($answer1, true, $quiz));
        $quiz->getSteps()->add($this->getStep($answer2, true, $quiz));
        $quiz->getSteps()->add($this->getStep($answer3, false, $quiz));

        $quiz->setCurrentStep($quiz->getSteps()->last());

        $this->quizRepository->expects($this->once())
            ->method('getIncompleteQuizByChat')
            ->with($chat)
            ->willReturn($quiz);

        $this->flusher->expects($this->once())
            ->method('flush');

        $quiz = $this->service->check($chat, $answer1);

        $this->assertTrue($quiz->isComplete());
    }

    /**
     * @throws ReflectionException
     */
    public function testDoReturnStepWithCorrectAnswerIfAnswerIsCorrect(): void
    {
        $answer = 'answer';
        $method = $this->createDoMethod();
        $step   = $this->getStep($answer, false, $this->getQuiz());

        /** @var QuizStep $resultStep */
        $resultStep = $method->invoke($this->service, $step, $answer);

        $this->assertTrue($resultStep->isAnswered());
        $this->assertTrue($resultStep->isCorrect());
    }

    /**
     * @throws ReflectionException
     */
    public function testDoReturnStepWithWrongAnswerIfAnswerDoesNotCorrect(): void
    {
        $answer = 'answer';
        $wrong  = 'wrong';
        $method = $this->createDoMethod();
        $step   = $this->getStep($answer, false, $this->getQuiz());

        /** @var QuizStep $resultStep */
        $resultStep = $method->invoke($this->service, $step, $wrong);

        $this->assertTrue($resultStep->isAnswered());
        $this->assertFalse($resultStep->isCorrect());
    }

    /**
     * @throws ReflectionException
     */
    private function createDoMethod(): ReflectionMethod
    {
        $class  = new ReflectionClass(AnswerChecker::class);
        $method = $class->getMethod('do');

        $method->setAccessible(true);

        return $method;
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

    private function getStep(string $answer, bool $isAnswered, ?Quiz $quiz = null): QuizStep
    {
        $step = new QuizStep($quiz);
        $step->setIsAnswered($isAnswered);
        $step->setCorrectWord($this->getWord($answer));

        if ($quiz) {
            $step->setQuiz($quiz);
        }

        return $step;
    }

    private function getQuiz(): Quiz
    {
        $quiz = new Quiz();
        $quiz->setId(22);

        return $quiz;
    }

    private function getChat(): Chat
    {
        $chat = new Chat();
        $chat->setId(1);

        return $chat;
    }
}
