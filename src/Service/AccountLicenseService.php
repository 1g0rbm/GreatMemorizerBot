<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Billing\License;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Service\Billing\AccountPrivilegesChecker;
use Throwable;

class AccountLicenseService
{
    private AccountRepository $accountRepository;

    private LicenseRepository $licenseRepository;

    private AccountPrivilegesChecker $checker;

    private EntityFlusher $flusher;

    public function __construct(
        AccountRepository $accountRepository,
        LicenseRepository $licenseRepository,
        AccountPrivilegesChecker $checker,
        EntityFlusher $flusher
    ) {
        $this->accountRepository = $accountRepository;
        $this->licenseRepository = $licenseRepository;
        $this->checker           = $checker;
        $this->flusher           = $flusher;
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function createLicense(int $accountId): ?License
    {
        $account = $this->accountRepository->getOneById($accountId);
        if ($this->checker->isFull($account)) {
            return null;
        }

        $license = License::createDefaultForAccount($account);

        $this->licenseRepository->addLicense($license);
        $this->flusher->flush();

        return $license;
    }
}
