<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Registry\QuizCreatorRegistry;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Service\Quiz\QuizManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group quiz
 */
class QuizManagerUnitTest extends TestCase
{
    private QuizManager $service;

    /** @var QuizRepository|MockObject */
    private QuizRepository $quizRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->quizRepository  = $this->createMock(QuizRepository::class);

        $registry = new QuizCreatorRegistry();
        $registry->addCreator(new QuizCreatorMock(Quiz::FROM_ALL));

        $this->service = new QuizManager($this->quizRepository, $registry);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testReturnQuizIfThereIsIncompleteQuiz(): void
    {
        $type       = Quiz::FROM_ALL;
        $chat       = $this->createChat();
        $sourceQuiz = $this->createQuiz();

        $this->quizRepository
            ->expects($this->once())
            ->method('findIncompleteByChatAndType')
            ->with($chat, $type)
            ->willReturn($sourceQuiz);

        $quiz = $this->service->get($chat, $type);

        $this->assertEquals($sourceQuiz, $quiz);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testCreateNewQuizIfThereIsNoQuiz()
    {
        $type    = Quiz::FROM_ALL;
        $chat    = $this->createChat();

        $this->quizRepository
            ->expects($this->once())
            ->method('findIncompleteByChatAndType')
            ->with($chat, $type)
            ->willReturn(null);

        $quiz = $this->service->get($chat, $type);

        $this->assertEquals($quiz->getChat(), $chat);
        $this->assertEquals($quiz->getType(), $type);
    }

    private function createChat(): Chat
    {
        $chat = new Chat();
        $chat->setId(1);

        return $chat;
    }

    private function createQuiz(): Quiz
    {
        $quiz = new Quiz();
        $quiz->setChat($this->createChat());

        return $quiz;
    }
}
