<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Billing\Limiter;

use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Service\Billing\AccountPrivilegesChecker;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

use function sprintf;

class LicenseLimiter
{
    public const DEFAULT_LIMIT = 10;

    private AccountPrivilegesChecker $checker;

    private AdapterInterface $cache;

    public function __construct(AccountPrivilegesChecker $checker, AdapterInterface $cache)
    {
        $this->checker = $checker;
        $this->cache   = $cache;
    }

    /**
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function isLimitReached(
        Account $account,
        string $key,
        DateTimeImmutable $expireAt,
        int $limit = self::DEFAULT_LIMIT
    ): bool {
        if ($this->checker->isFull($account)) {
            return false;
        }

        $item  = $this->cache->getItem(sprintf('%d_%s', $account->getId(), $key));
        $count = $item->isHit() ? $item->get() : 0;

        if ($count >= $limit) {
            return true;
        }

        $item->set($count + 1);
        $item->expiresAt($expireAt);

        $this->cache->save($item);

        return false;
    }
}
