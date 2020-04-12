<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Billing\Limiter;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Repository\Quiz\QuizReminderRepository;
use Ig0rbm\Memo\Service\Billing\AccountPrivilegesChecker;

class ReminderLicenseLimiter
{
    public const DEFAULT_LIMIT = 1;

    private AccountPrivilegesChecker $checker;

    private QuizReminderRepository $reminderRepository;

    public function __construct(AccountPrivilegesChecker $checker, QuizReminderRepository $reminderRepository)
    {
        $this->checker            = $checker;
        $this->reminderRepository = $reminderRepository;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function isLimitReached(Account $account, int $limit = self::DEFAULT_LIMIT): bool
    {
        if ($this->checker->isFull($account)) {
            return false;
        }

        $count = $this->reminderRepository->countRemindersForChat($account->getChat());
        if ($count < $limit) {
            return false;
        }

        return true;
    }
}
