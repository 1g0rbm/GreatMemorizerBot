<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Billing;

use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;

class AccountPrivilegesChecker
{
    public const DEMO_PRIVILEGES = 'DEMO';
    public const FULL_PRIVILEGES = 'FULL';

    private LicenseRepository $licenseRepository;

    public function __construct(LicenseRepository $licenseRepository)
    {
        $this->licenseRepository = $licenseRepository;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function isDemo(Account $account): bool
    {
        return $this->check($account) === self::DEMO_PRIVILEGES;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function isFull(Account $account): bool
    {
        return $this->check($account) === self::FULL_PRIVILEGES;
    }

    /**
     * @throws NonUniqueResultException
     */
    private function check(Account $account): string
    {
        $license = $this->licenseRepository->findLatestLicenseByAccount($account);
        if ($license === null) {
            return self::DEMO_PRIVILEGES;
        }

        if ($license->getDateEnd()->getTimestamp() < (new DateTimeImmutable())->getTimestamp()) {
            return self::DEMO_PRIVILEGES;
        }

        return self::FULL_PRIVILEGES;
    }
}
