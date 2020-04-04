<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Billing\Limiter;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Billing\AccountPrivilegesChecker;
use Doctrine\DBAL\DBALException;

class WordListLicenseLimiter
{
    public const DEFAULT_LIMIT = 10;

    private AccountPrivilegesChecker $checker;

    private WordListRepository $wordListRepository;

    public function __construct(AccountPrivilegesChecker $checker, WordListRepository $wordListRepository)
    {
        $this->checker            = $checker;
        $this->wordListRepository = $wordListRepository;
    }

    /**
     * @throws NonUniqueResultException
     * @throws DBALException
     */
    public function isLimitReached(Account $account, int $limit = self::DEFAULT_LIMIT): bool
    {
        if ($this->checker->isFull($account)) {
            return false;
        }

        $count = $this->wordListRepository->countUniqueWords($account->getChat());
        if ($count < $limit) {
            return false;
        }

        return true;
    }
}
