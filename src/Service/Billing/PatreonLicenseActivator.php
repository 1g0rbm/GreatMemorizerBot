<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Billing;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Billing\License;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Repository\Billing\Patreon\PledgeRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Throwable;

class PatreonLicenseActivator
{
    private LicenseCreator $licenseCreator;

    private PledgeRepository $pledgeRepository;

    private LicenseRepository $licenseRepository;

    private EntityFlusher $flusher;

    public function __construct(
        LicenseCreator $licenseCreator,
        PledgeRepository $pledgeRepository,
        LicenseRepository $licenseRepository,
        EntityFlusher $flusher
    ) {
        $this->licenseCreator    = $licenseCreator;
        $this->pledgeRepository  = $pledgeRepository;
        $this->licenseRepository = $licenseRepository;
        $this->flusher           = $flusher;
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function activate(Account $account, string $email): ?License
    {
        $pledge = $this->pledgeRepository->findOneByEmail($email);
        if ($pledge === null) {
            return null;
        }

        if ($pledge->getAccount()) {
            return null;
        }

        $pledge->setAccount($account);

        $license = $this->licenseCreator->create($account, License::PROVIDER_PATREON);

        $this->licenseRepository->addLicense($license);
        $this->flusher->flush();

        return $license;
    }
}
