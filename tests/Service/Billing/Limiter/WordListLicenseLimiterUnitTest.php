<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Billing\Limiter;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Billing\AccountPrivilegesChecker;
use Ig0rbm\Memo\Service\Billing\Limiter\WordListLicenseLimiter;
use PHPUnit\Framework\TestCase;

class WordListLicenseLimiterUnitTest extends TestCase
{
    private WordListLicenseLimiter $limiter;

    private AccountPrivilegesChecker $checker;

    private WordListRepository $wordListRepository;

    public function setUp(): void
    {
        $this->checker            = $this->createMock(AccountPrivilegesChecker::class);
        $this->wordListRepository = $this->createMock(WordListRepository::class);

        $this->limiter = new WordListLicenseLimiter($this->checker, $this->wordListRepository);
    }

    /**
     * @throws DBALException
     * @throws NonUniqueResultException
     */
    public function testIsLimitReachedReturnFalseIfThereIsFullLicense(): void
    {
        $account = new Account();

        $this->checker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(true);

        $this->wordListRepository->expects($this->never())->method('countUniqueWords');

        $this->assertFalse($this->limiter->isLimitReached($account));
    }

    /**
     * @throws DBALException
     * @throws NonUniqueResultException
     */
    public function testIsLimitReachedReturnFalseIfLimitDoesNotReached(): void
    {
        $chat    = new Chat();
        $account = new Account();
        $account->setChat($chat);

        $this->checker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(false);

        $this->wordListRepository->expects($this->once())
            ->method('countUniqueWords')
            ->with($chat)
            ->willReturn(5);

        $this->assertFalse($this->limiter->isLimitReached($account, 10));
    }

    /**
     * @throws DBALException
     * @throws NonUniqueResultException
     */
    public function testIsLimitReachedReturnTrueIfLimitReached(): void
    {
        $chat    = new Chat();
        $account = new Account();
        $account->setChat($chat);

        $this->checker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(false);

        $this->wordListRepository->expects($this->once())
            ->method('countUniqueWords')
            ->with($chat)
            ->willReturn(10);

        $this->assertFalse($this->limiter->isLimitReached($account, 10));
    }
}
