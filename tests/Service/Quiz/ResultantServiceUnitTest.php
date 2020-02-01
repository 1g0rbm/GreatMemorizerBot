<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Exception\Quiz\ResultantException;
use Ig0rbm\Memo\Service\Quiz\ResultantService;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use Ig0rbm\Memo\Service\Telegram\TranslationService;
use PHPUnit\Framework\TestCase;

class ResultantServiceUnitTest extends TestCase
{
    private ResultantService $service;

    private TranslationService $translator;

    public function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslationService::class);

        $this->service = new ResultantService(new MessageBuilder(), $this->translator);
    }

    public function testCreateThrowExceptionIfQuizIsIncomplete(): void
    {
        $quiz = $this->createIncompleteQuiz();

        $this->expectException(ResultantException::class);

        $this->service->create($quiz);
    }

    public function testCreateReturnString(): void
    {
        $quiz = $this->createCompleteQuiz();

        $this->assertInternalType('string', $this->service->create($quiz));
    }

    private function createCompleteQuiz(): Quiz
    {
        $chat = new Chat();
        $chat->setId(1);

        $quiz = new Quiz();
        $quiz->setId(1);
        $quiz->setIsComplete(true);
        $quiz->setChat($chat);

        return $quiz;
    }

    private function createIncompleteQuiz(): Quiz
    {
        $chat = new Chat();
        $chat->setId(1);

        $quiz = new Quiz();
        $quiz->setId(1);
        $quiz->setChat($chat);

        return $quiz;
    }
}
