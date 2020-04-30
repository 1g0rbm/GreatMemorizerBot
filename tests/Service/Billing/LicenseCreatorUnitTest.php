<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Billing;

use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Billing\License;
use Ig0rbm\Memo\Exception\Billing\LicenseCreateException;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Service\Billing\LicenseCreator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

use function sprintf;

class LicenseCreatorUnitTest extends TestCase
{
    private LicenseCreator $service;

    /** @var LicenseRepository|MockObject */
    private LicenseRepository $licenseRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->licenseRepository = $this->createMock(LicenseRepository::class);

        $this->service = new LicenseCreator($this->licenseRepository);
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function testCreateThrowExceptionIfThereIsInvalidProvider(): void
    {
        $account = new Account();
        $provider = 'wrong_provider';

        $this->expectException(LicenseCreateException::class);

        $this->service->create($account, $provider);
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function testCreateReturnLicenseWithTodayStartDateIfThereIsNoLicense(): void
    {
        $account  = new Account();
        $provider = 'patreon';
        $today    = new DateTimeImmutable();
        $endDate  = new DateTimeImmutable(sprintf('+ %d years', 5));

        $this->licenseRepository->expects($this->once())
            ->method('findCurrentLicenseForAccount')
            ->with($account)
            ->willReturn(null);

        $license = $this->service->create($account, $provider);

        $this->assertEquals(
            $license->getDateStart()->format('d-m-y'),
            $today->format('d-m-y')
        );

        $this->assertEquals(
            $license->getDateEnd()->format('d-m-y'),
            $endDate->format('d-m-y')
        );
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function testCreateReturnLicenseWithAnotherStartDateIfThereIsLicense(): void
    {
        $account  = new Account();
        $provider = 'patreon';
        $today    = new DateTimeImmutable();
        $endDate  = new DateTimeImmutable(sprintf('+ %d years', 5));
        $license  = new License($account, $today, $endDate, $provider);

        $this->licenseRepository->expects($this->once())
            ->method('findCurrentLicenseForAccount')
            ->with($account)
            ->willReturn($license);

        $createdLicense = $this->service->create($account, $provider);

        $this->assertEquals(
            $license->getDateEnd()->format('d-m-y'),
            $createdLicense->getDateStart()->format('d-m-y')
        );
        $this->assertEquals(
            $createdLicense->getDateStart()->modify(sprintf('+ %d years', 5))->format('d-m-y'),
            $createdLicense->getDateEnd()->format('d-m-y')
        );
    }
}
