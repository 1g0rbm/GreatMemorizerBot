<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Billing\Limiter;

use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Service\Billing\AccountPrivilegesChecker;
use Ig0rbm\Memo\Service\Billing\Limiter\LicenseLimiter;
use Ig0rbm\Memo\Tests\CacheAdapter;
use Ig0rbm\Memo\Tests\CacheItem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;

use function sprintf;

/**
 * @group unit
 * @group billing
 * @group limiter
 */
class LicenseLimiterUnitTest extends TestCase
{
    private LicenseLimiter $service;

    /** @var AccountPrivilegesChecker|MockObject */
    private AccountPrivilegesChecker $accountChecker;

    public function setUp(): void
    {
        $this->accountChecker = $this->createMock(AccountPrivilegesChecker::class);
    }

    /**
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     */
    public function testIsLimitReachedReturnFalseIfCacheIsEmpty(): void
    {
        $key     = 'test_key';
        $expire  = new DateTimeImmutable('tomorrow midnight');
        $account = new Account();
        $account->setId(11);

        $this->createService();

        $this->accountChecker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(false);

        $this->assertFalse($this->service->isLimitReached($account, $key, $expire));
    }

    /**
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     */
    public function testIsLimitReachedReturnTrueIfLimitReached(): void
    {
        $key     = 'test_key';
        $expire  = new DateTimeImmutable('tomorrow midnight');
        $account = new Account();
        $account->setId(11);

        $cacheKey = sprintf('%d_%s', $account->getId(), $key);
        $item     = new CacheItem($cacheKey, true);
        $item->set(21);

        $this->createService([$cacheKey => $item]);

        $this->accountChecker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(false);

        $this->assertTrue($this->service->isLimitReached($account, $key, $expire, 20));
    }

    /**
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     */
    public function testIsLimitReachedReturnFalseIfLimitNotReached(): void
    {
        $key     = 'test_key';
        $expire  = new DateTimeImmutable('tomorrow midnight');
        $account = new Account();
        $account->setId(11);

        $cacheKey = sprintf('%d_%s', $account->getId(), $key);
        $item     = new CacheItem($cacheKey, true);
        $item->set(5);

        $this->createService([$cacheKey => $item]);

        $this->accountChecker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(false);

        $this->assertFalse($this->service->isLimitReached($account, $key, $expire));
    }

    /**
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function testIsLimitReachedReturnFalseIfThereIsFullLicense(): void
    {
        $this->createService();
        $account = new Account();
        $key     = 'test_key';
        $expire  = new DateTimeImmutable('tomorrow midnight');

        $this->accountChecker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(true);

        $this->assertFalse($this->service->isLimitReached($account, $key, $expire));
    }

    /**
     * @param array|CacheItemInterface[] $storage
     */
    private function createService(array $storage = [])
    {
        $this->service = new LicenseLimiter($this->accountChecker, new CacheAdapter($storage));
    }
}
