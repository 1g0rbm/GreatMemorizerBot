<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Service\Billing\Limiter\LicenseLimiter;
use Ig0rbm\Memo\Service\Quiz\QuizBuilder;
use Ig0rbm\Memo\Service\Quiz\QuizManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;

/**
 * @group unit
 * @group quiz
 */
class QuizManagerUnitTest extends TestCase
{
    /** @var QuizManager */
    private $service;

    /** @var QuizBuilder|MockObject */
    private QuizBuilder $quizBuilder;

    /** @var QuizRepository|MockObject */
    private QuizRepository $quizRepository;

    /** @var AccountRepository|MockObject */
    private AccountRepository $accountRepository;

    /** @var LicenseLimiter|MockObject */
    private LicenseLimiter $limiter;

    public function setUp(): void
    {
        parent::setUp();

        $this->quizBuilder       = $this->createMock(QuizBuilder::class);
        $this->quizRepository    = $this->createMock(QuizRepository::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->limiter           = $this->createMock(LicenseLimiter::class);

        $this->service = new QuizManager(
            $this->quizBuilder,
            $this->quizRepository,
            $this->accountRepository,
            $this->limiter
        );
    }

    /**
     * @throws DBALException
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
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
