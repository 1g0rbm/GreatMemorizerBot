<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Billing\Limiter;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\Billing\AccountPrivilegesChecker;
use Ig0rbm\Memo\Service\Billing\Limiter\TranslateWordsLicenseLimiter;
use Ig0rbm\Memo\Tests\CacheAdapter;
use Ig0rbm\Memo\Tests\CacheItem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;

use function sprintf;

class TranslateWordsLicenseLimiterUnitTest extends TestCase
{
    private TranslateWordsLicenseLimiter $service;

    /** @var AccountPrivilegesChecker|MockObject */
    private AccountPrivilegesChecker $accountChecker;

    /** @var AccountRepository|MockObject */
    private AccountRepository $accountRepository;

    public function setUp(): void
    {
        $this->accountChecker    = $this->createMock(AccountPrivilegesChecker::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
    }

    /**
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     */
    public function testIsLimitReachedReturnFalseIfCacheIsEmpty(): void
    {
        $chat    = $this->createChat();
        $account = new Account();
        $account->setChat($chat);
        $account->setChatId($chat->getId());
        $account->setId(11);

        $this->createService();

        $this->accountRepository->expects($this->once())
            ->method('getOneByChatId')
            ->with($chat->getId())
            ->willReturn($account);

        $this->accountChecker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(false);

        $this->assertFalse($this->service->isLimitReached($chat));
    }

    /**
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     */
    public function testIsLimitReachedReturnTrueIfLimitReached(): void
    {
        $chat    = $this->createChat();
        $account = new Account();
        $account->setChat($chat);
        $account->setChatId($chat->getId());
        $account->setId(11);

        $cacheKey = sprintf('%d_word_translate_limit', $account->getId());
        $item     = new CacheItem($cacheKey, true);
        $item->set(21);

        $this->createService([$cacheKey => $item]);

        $this->accountRepository->expects($this->once())
            ->method('getOneByChatId')
            ->with($chat->getId())
            ->willReturn($account);

        $this->accountChecker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(false);

        $this->assertTrue($this->service->isLimitReached($chat));
    }

    /**
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     */
    public function testIsLimitReachedReturnFalseIfLimitNotReached(): void
    {
        $chat    = $this->createChat();
        $account = new Account();
        $account->setChat($chat);
        $account->setChatId($chat->getId());
        $account->setId(11);

        $cacheKey = sprintf('%d_word_translate_limit', $account->getId());
        $item     = new CacheItem($cacheKey, true);
        $item->set(5);

        $this->createService([$cacheKey => $item]);

        $this->accountRepository->expects($this->once())
            ->method('getOneByChatId')
            ->with($chat->getId())
            ->willReturn($account);

        $this->accountChecker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(false);

        $this->assertFalse($this->service->isLimitReached($chat));
    }

    /**
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function testIsLimitReachedReturnFalseIfThereIsFullLicense(): void
    {
        $this->createService();

        $chat    = $this->createChat();
        $account = new Account();
        $account->setChat($chat);
        $account->setChatId($chat->getId());

        $this->accountRepository->expects($this->once())
            ->method('getOneByChatId')
            ->with($chat->getId())
            ->willReturn($account);

        $this->accountChecker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(true);

        $this->assertFalse($this->service->isLimitReached($chat));
    }

    /**
     * @param array|CacheItemInterface[] $storage
     */
    private function createService(array $storage = [])
    {
        $this->service = new TranslateWordsLicenseLimiter(
            $this->accountChecker,
            new CacheAdapter($storage),
            $this->accountRepository
        );
    }

    private function createChat(): Chat
    {
        $chat = new Chat();
        $chat->setId(1);

        return $chat;
    }
}
