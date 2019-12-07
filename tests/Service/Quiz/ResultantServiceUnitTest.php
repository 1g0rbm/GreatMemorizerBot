<?php

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Exception\Quiz\ResultantException;
use Ig0rbm\Memo\Service\Quiz\ResultantService;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use PHPUnit\Framework\TestCase;

class ResultantServiceUnitTest extends TestCase
{
    /** @var ResultantService */
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new ResultantService(new MessageBuilder());
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
        $quiz = new Quiz();
        $quiz->setId(1);
        $quiz->setIsComplete(true);

        return $quiz;
    }

    private function createIncompleteQuiz(): Quiz
    {
        $quiz = new Quiz();
        $quiz->setId(1);

        return $quiz;
    }
}
