<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service;

use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Billing\License;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Service\AccountLicenseService;
use Ig0rbm\Memo\Service\Billing\AccountPrivilegesChecker;
use Ig0rbm\Memo\Service\EntityFlusher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\NonUniqueResultException;
use Throwable;

class AccountLicenseServiceUnitTest extends TestCase
{
    private AccountLicenseService $service;

    /** @var AccountRepository|MockObject */
    private AccountRepository $accountRepository;

    /** @var LicenseRepository|MockObject */
    private LicenseRepository $licenseRepository;

    /** @var AccountPrivilegesChecker|MockObject */
    private AccountPrivilegesChecker $checker;

    /** @var EntityFlusher|MockObject */
    private EntityFlusher $flusher;

    public function setUp(): void
    {
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->licenseRepository = $this->createMock(LicenseRepository::class);
        $this->checker           = $this->createMock(AccountPrivilegesChecker::class);
        $this->flusher           = $this->createMock(EntityFlusher::class);

        $this->service = new AccountLicenseService(
            $this->accountRepository,
            $this->licenseRepository,
            $this->checker,
            $this->flusher
        );
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function testCreateLicenseReturnNullIfThereIsFullLicenseForAccount(): void
    {
        $accountId = 1;
        $account   = new Account();
        $account->setId($accountId);

        $this->accountRepository->expects($this->once())
            ->method('getOneById')
            ->with($accountId)
            ->willReturn($account);

        $this->checker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(true);

        $this->assertNull($this->service->createLicense($accountId));
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function testCreateLicenseReturnLicenseIfThereIsNotOtherActiveLicenses(): void
    {
        $accountId = 1;
        $account   = new Account();
        $account->setId($accountId);

        $this->accountRepository->expects($this->once())
            ->method('getOneById')
            ->with($accountId)
            ->willReturn($account);

        $this->checker->expects($this->once())
            ->method('isFull')
            ->with($account)
            ->willReturn(false);

        $this->licenseRepository->expects($this->once())
            ->method('addLicense');

        $this->flusher->expects($this->once())
            ->method('flush');

        $license = $this->service->createLicense($accountId);

        $this->assertInstanceOf(License::class, $license);
        $this->assertEquals($account, $license->getAccount());
        $this->assertEquals(License::PROVIDER_DEFAULT, $license->getProvider());
    }
}
