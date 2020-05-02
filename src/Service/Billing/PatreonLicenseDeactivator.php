<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Billing;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Billing\License;
use Ig0rbm\Memo\Exception\Billing\PatreonLicenseDeactivateException;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Repository\Billing\Patreon\PledgeRepository;

class PatreonLicenseDeactivator
{
    private PledgeRepository $pledgeRepository;

    private LicenseRepository $licenseRepository;

    private EntityManagerInterface $em;

    public function __construct(
        PledgeRepository $pledgeRepository,
        LicenseRepository $licenseRepository,
        EntityManagerInterface $em
    ) {
        $this->pledgeRepository  = $pledgeRepository;
        $this->licenseRepository = $licenseRepository;
        $this->em                = $em;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function deactivate(string $email): License
    {
        $pledge = $this->pledgeRepository->findOneByEmail($email);
        if ($pledge === null) {
            throw PatreonLicenseDeactivateException::notFoundPledgebyEmail($email);
        }

        $account = $pledge->getAccount();
        if ($account === null) {
            throw PatreonLicenseDeactivateException::pledgeDoesNotHaveAccount($pledge->getId());
        }

        $license = $this->licenseRepository->findActiveLicenseByAccountAndProvider(
            $account,
            License::PROVIDER_PATREON
        );

        if ($license === null) {
            throw PatreonLicenseDeactivateException::licenseNotFoundForAccount($account->getId());
        }

        $license->setDateEnd(new DateTimeImmutable('last day of this month'));
        $this->em->remove($pledge);
        $this->em->flush();

        return $license;
    }
}
