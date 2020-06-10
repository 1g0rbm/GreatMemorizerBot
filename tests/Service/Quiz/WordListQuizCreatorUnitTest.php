<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Exception\Billing\LicenseLimitReachedException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Billing\Limiter\LicenseLimiter;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Quiz\Creator\WordListQuizCreator;
use Ig0rbm\Memo\Service\Quiz\QuizStepBuilderByWordList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;
use DateTimeImmutable;

class WordListQuizCreatorUnitTest extends TestCase
{
    private WordListQuizCreator $service;

    /** @var AccountRepository|MockObject */
    private AccountRepository $accountRepository;

    /** @var WordListRepository|MockObject */
    private WordListRepository $wordListRepository;

    /** @var QuizRepository|MockObject */
    private QuizRepository $quizRepository;

    /** @var LicenseLimiter|MockObject */
    private LicenseLimiter $licenseLimiter;

    /** @var QuizStepBuilderByWordList|MockObject */
    private QuizStepBuilderByWordList $quizStepBuilder;

    /** @var EntityFlusher|MockObject */
    private EntityFlusher $flusher;

    public function setUp(): void
    {
        $this->accountRepository  = $this->createMock(AccountRepository::class);
        $this->wordListRepository = $this->createMock(WordListRepository::class);
        $this->quizRepository     = $this->createMock(QuizRepository::class);
        $this->licenseLimiter     = $this->createMock(LicenseLimiter::class);
        $this->quizStepBuilder    = $this->createMock(QuizStepBuilderByWordList::class);
        $this->flusher            = $this->createMock(EntityFlusher::class);

        $this->service = new WordListQuizCreator(
            $this->accountRepository,
            $this->wordListRepository,
            $this->quizRepository,
            $this->licenseLimiter,
            $this->quizStepBuilder,
            $this->flusher
        );
    }

    /**
     * @throws DBALException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws InvalidArgumentException
     */
    public function testCreateThrowExceptionIfLimitReached(): void
    {
        $chat = $this->createChat();
        $account = $this->accountRepositoryGetAccountBehavior($chat);

        $this->createReachedLimitBehavior($account, true);

        $this->expectException(LicenseLimitReachedException::class);

        $this->service->create($chat);
    }

    /**
     * @throws DBALException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function testCreateReturnQuiz(): void
    {
        $chat = $this->createChat();
        $account = $this->accountRepositoryGetAccountBehavior($chat);

        $this->createReachedLimitBehavior($account, false);

        $wordList = $this->createWordListRepositoryCreator($chat);

        $quiz = $this->service->create($chat);

        $this->assertInstanceOf(Quiz::class, $quiz);
        $this->assertSame($wordList, $quiz->getWordList());
        $this->assertSame($chat, $quiz->getChat());
    }

    private function createWordListRepositoryCreator(Chat $chat): WordList
    {
        $wordList = new WordList();
        $wordList->setId(1);
        $wordList->setChat($chat);

        $this->wordListRepository
            ->expects($this->once())
            ->method('getOneByChat')
            ->with($chat)
            ->willReturn($wordList);

        return $wordList;
    }

    private function createReachedLimitBehavior(Account $account, bool $isReached): void
    {
        $this->licenseLimiter
            ->expects($this->once())
            ->method('isLimitReached')
            ->with($account, 'list_quiz_limit', new DateTimeImmutable('tomorrow midnight'), 1)
            ->willReturn($isReached);
    }

    private function accountRepositoryGetAccountBehavior(Chat $chat): Account
    {
        $account = new Account();
        $account->setId(1);
        $account->setChat($chat);

        $this->accountRepository
            ->expects($this->once())
            ->method('getOneByChatId')
            ->with($chat->getId())
            ->willReturn($account);

        return $account;
    }

    private function createChat(): Chat
    {
        $chat = new Chat();
        $chat->setId(1);

        return $chat;
    }
}
