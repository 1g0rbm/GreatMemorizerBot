<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Billing\License;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Service\Billing\AccountPrivilegesChecker;
use Ig0rbm\Memo\Service\Billing\LicenseCreator;
use Throwable;

class AccountLicenseService
{
    private AccountRepository $accountRepository;

    private LicenseRepository $licenseRepository;

    private LicenseCreator $licenseCreator;

    private AccountPrivilegesChecker $checker;

    private EntityFlusher $flusher;

    public function __construct(
        AccountRepository $accountRepository,
        LicenseRepository $licenseRepository,
        LicenseCreator $licenseCreator,
        AccountPrivilegesChecker $checker,
        EntityFlusher $flusher
    ) {
        $this->accountRepository = $accountRepository;
        $this->licenseRepository = $licenseRepository;
        $this->licenseCreator    = $licenseCreator;
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

        $license = $this->licenseCreator->create($account, License::PROVIDER_DEFAULT);

        $this->licenseRepository->addLicense($license);
        $this->flusher->flush();

        return $license;
    }
}
