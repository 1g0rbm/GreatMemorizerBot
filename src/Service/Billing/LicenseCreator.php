<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Billing;

use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Billing\License;
use Ig0rbm\Memo\Exception\Billing\LicenseCreateException;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Throwable;

use function sprintf;

class LicenseCreator
{
    private LicenseRepository $licenseRepository;

    public function __construct(LicenseRepository $licenseRepository)
    {
        $this->licenseRepository = $licenseRepository;
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function create(Account $account, string $provider): License
    {
        $dateStart = $this->getDateStart($account);
        $dateEnd   = $this->getDateEnd($provider, $dateStart);

        return new License($account, $dateStart, $dateEnd, $provider);
    }

    /**
     * @throws NonUniqueResultException
     */
    private function getDateStart(Account $account): DateTimeImmutable
    {
        $license = $this->licenseRepository->findCurrentLicenseForAccount($account);

        return $license ? $license->getDateEnd() : new DateTimeImmutable();
    }

    /**
     * @throws Throwable
     */
    private function getDateEnd(string $provider, DateTimeImmutable $dateStart): DateTimeImmutable
    {
        if ($provider === License::PROVIDER_PATREON) {
            return $dateStart->modify(sprintf('+ %d years', 5));
        } elseif ($provider === License::PROVIDER_DEFAULT) {
            return $dateStart->modify(sprintf('+ %d months', 3));
        }

        throw LicenseCreateException::invalidProvider($provider);
    }
}
