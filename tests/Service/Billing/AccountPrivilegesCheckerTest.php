<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Billing;

use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Billing\License;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Service\Billing\AccountPrivilegesChecker;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\NonUniqueResultException;
use DateTimeImmutable;

class AccountPrivilegesCheckerTest extends TestCase
{
    private AccountPrivilegesChecker $service;

    private LicenseRepository $licenseRepository;

    public function setUp(): void
    {
        $this->licenseRepository = $this->createMock(LicenseRepository::class);

        $this->service = new AccountPrivilegesChecker($this->licenseRepository);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testIsFullReturnFalseIfThereIsNoLicenseForAccount(): void
    {
        $account = new Account();

        $this->licenseRepository->expects($this->once())
            ->method('findLatestLicenseByAccount')
            ->with($account)
            ->willReturn(null);

        $this->assertFalse($this->service->isFull($account));
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testIsFullReturnFalseIfEndDateLessThenNow(): void
    {
        $account = new Account();
        $license = new License(
            $account,
            new DateTimeImmutable(),
            new DateTimeImmutable('-1 hour'),
            License::PROVIDER_DEFAULT
        );

        $this->licenseRepository->expects($this->once())
            ->method('findLatestLicenseByAccount')
            ->with($account)
            ->willReturn($license);

        $this->assertFalse($this->service->isFull($account));
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testIsFullReturnTrueIfEndDateGreaterThenNow(): void
    {
        $account = new Account();
        $license = new License(
            $account,
            new DateTimeImmutable(),
            new DateTimeImmutable('+1 hour'),
            License::PROVIDER_DEFAULT
        );

        $this->licenseRepository->expects($this->once())
            ->method('findLatestLicenseByAccount')
            ->with($account)
            ->willReturn($license);

        $this->assertTrue($this->service->isFull($account));
    }
}
