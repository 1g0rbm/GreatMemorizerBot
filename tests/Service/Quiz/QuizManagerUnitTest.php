<?php

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Service\Quiz\QuizBuilder;
use Ig0rbm\Memo\Service\Quiz\QuizManager;

/**
 * @group unit
 * @group quiz
 */
class QuizManagerUnitTest extends TestCase
{
    /** @var QuizManager */
    private $service;

    /** @var QuizBuilder|MockObject */
    private $quizBuilder;

    /** @var QuizRepository|MockObject */
    private $quizRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->quizBuilder    = $this->createMock(QuizBuilder::class);
        $this->quizRepository = $this->createMock(QuizRepository::class);

        $this->service = new QuizManager($this->quizBuilder, $this->quizRepository);
    }

    /**
     * @throws DBALException
     * @throws ORMException
     */
    public function testGetQuizByChatReturnExistedIncompleteQuiz(): void
    {
        $expectedQuiz = $this->createQuiz();

        $this->quizRepository->expects($this->once())
            ->method('findIncompleteQuizByChat')
            ->with($expectedQuiz->getChat())
            ->willReturn($expectedQuiz);

        $this->quizBuilder->expects($this->never())
            ->method('build');

        $quiz = $this->service->getQuizByChat($expectedQuiz->getChat());

        $this->assertEquals($expectedQuiz, $quiz);
    }

    /**
     * @throws DBALException
     * @throws ORMException
     */
    public function testGetQuizByChatReturnNewQuiz(): void
    {
        $expectedQuiz = $this->createQuiz();

        $this->quizRepository->expects($this->once())
            ->method('findIncompleteQuizByChat')
            ->with($expectedQuiz->getChat())
            ->willReturn(null);

        $this->quizBuilder->expects($this->once())
            ->method('build')
            ->with($expectedQuiz->getChat())
            ->willReturn($expectedQuiz);

        $quiz = $this->service->getQuizByChat($expectedQuiz->getChat());

        $this->assertEquals($expectedQuiz, $quiz);
    }

    private function createChat(): Chat
    {
        $chat = new Chat();

        return $chat;
    }

    private function createQuiz(): Quiz
    {
        $quiz = new Quiz();
        $quiz->setChat($this->createChat());

        return $quiz;
    }
}
