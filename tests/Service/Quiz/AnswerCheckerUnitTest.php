<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Telegram\Message\Text;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Quiz\AnswerChecker;
use Ig0rbm\Memo\Service\Quiz\Rotator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AnswerCheckerUnitTest extends TestCase
{
    private AnswerChecker $service;

    /** @var QuizRepository|MockObject */
    private QuizRepository $quizRepository;

    /** @var EntityFlusher|MockObject */
    private EntityFlusher $flusher;

    public function setUp(): void
    {
        parent::setUp();

        $this->quizRepository = $this->createMock(QuizRepository::class);
        $this->flusher        = $this->createMock(EntityFlusher::class);

        $this->service = new AnswerChecker($this->quizRepository, new Rotator(), $this->flusher);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testThrowExceptionIfThereIsAnsweredStep(): void
    {
        $chat = $this->getChat();
        $text = $this->getText();
        $quiz = $this->getQuiz();
        $step = $this->getStep($text->getCommand(), true, $quiz);
        $quiz->getSteps()->add($step);
        $quiz->setCurrentStep($step);

        $this->quizRepository->expects($this->once())
            ->method('getIncompleteQuizByChat')
            ->with($chat)
            ->willReturn($quiz);

        $this->expectException(QuizStepException::class);

        $this->service->check($chat, $text);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testReturnIncompleteTestIfThereIsUnanswered(): void
    {
        $chat = $this->getChat();
        $text = $this->getText();
        $quiz = $this->getQuiz();
        $step = $this->getStep($text->getCommand(), false, $quiz);
        $quiz->getSteps()->add($step);
        $quiz->getSteps()->add($this->getStep($text->getCommand(), false, $quiz));
        $quiz->setCurrentStep($step);

        $this->quizRepository->expects($this->once())
            ->method('getIncompleteQuizByChat')
            ->with($chat)
            ->willReturn($quiz);

        $quiz = $this->service->check($chat, $text);

        $this->assertFalse($quiz->isComplete());
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testReturnCompleteTestIfThereIsNotUnansweredSteps(): void
    {
        $chat = $this->getChat();
        $text = $this->getText();
        $quiz = $this->getQuiz();
        $step = $this->getStep($text->getCommand(), false, $quiz);
        $quiz->getSteps()->add($step);
        $quiz->getSteps()->add($this->getStep($text->getCommand(), true, $quiz));
        $quiz->setCurrentStep($step);

        $this->quizRepository->expects($this->once())
            ->method('getIncompleteQuizByChat')
            ->with($chat)
            ->willReturn($quiz);

        $quiz = $this->service->check($chat, $text);

        $this->assertTrue($quiz->isComplete());
    }

    private function getText(): Text
    {
        $text = new Text();
        $text->setCommand('/command');
        $text->setParameter('w', '10');

        return $text;
    }

    private function getWord(string $text): Word
    {
        $translation = new Word();
        $translation->setText($text);
        $translation->setId(10);

        $translation2 = new Word();
        $translation2->setText($text);
        $translation2->setId(20);

        $word = new Word();
        $word->setText($text);
        $word->getTranslations()->add($translation);
        $word->getTranslations()->add($translation2);

        return $word;
    }

    private function getStep(string $answer, bool $isAnswered, ?Quiz $quiz = null): QuizStep
    {
        $word = $this->getWord($answer);
        $step = new QuizStep($quiz);
        $step->setIsAnswered($isAnswered);
        $step->setCorrectWord($this->getWord($answer));
        $step->getWords()->add($word);

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
