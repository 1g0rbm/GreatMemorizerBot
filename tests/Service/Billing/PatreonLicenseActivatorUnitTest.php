<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Billing;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Billing\License;
use Ig0rbm\Memo\Entity\Billing\Patreon\Pledge;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Repository\Billing\Patreon\PledgeRepository;
use Ig0rbm\Memo\Service\Billing\PatreonLicenseActivator;
use Ig0rbm\Memo\Service\EntityFlusher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class PatreonLicenseActivatorUnitTest extends TestCase
{
    private PatreonLicenseActivator $service;

    /** @var PledgeRepository|MockObject  */
    private PledgeRepository $pledgeRepository;

    /** @var LicenseRepository|MockObject  */
    private LicenseRepository $licenseRepository;

    /** @var EntityFlusher|MockObject  */
    private EntityFlusher $flusher;

    public function setUp(): void
    {
        $this->pledgeRepository  = $this->createMock(PledgeRepository::class);
        $this->licenseRepository = $this->createMock(LicenseRepository::class);
        $this->flusher           = $this->createMock(EntityFlusher::class);

        $this->service = new PatreonLicenseActivator($this->pledgeRepository, $this->licenseRepository, $this->flusher);
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function testReturnNullIfThereIsNoEmailIdDb(): void
    {
        $account = new Account();
        $email   = 'test@mail.com';

        $this->pledgeRepository->expects($this->once())
            ->method('findOneByEmail')
            ->with($email)
            ->willReturn(null);

        $this->flusher->expects($this->never())->method('flush');

        $this->assertNull($this->service->activate($account, $email));
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function testReturnNullIfThereIsAlreadySetAccount(): void
    {
        $account = new Account();
        $pledge  = new Pledge();
        $pledge->setAccount($account);
        $email   = 'test@mail.com';

        $this->pledgeRepository->expects($this->once())
            ->method('findOneByEmail')
            ->with($email)
            ->willReturn($pledge);

        $this->flusher->expects($this->never())->method('flush');

        $this->assertNull($this->service->activate($account, $email));
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function testReturnTrueIfThereIsEmailIdDb(): void
    {
        $pledge  = new Pledge();
        $account = new Account();
        $email   = 'test@mail.com';

        $this->pledgeRepository->expects($this->once())
            ->method('findOneByEmail')
            ->with($email)
            ->willReturn($pledge);

        $this->licenseRepository->expects($this->once())->method('addLicense');
        $this->flusher->expects($this->once())->method('flush');

        $license = $this->service->activate($account, $email);

        $this->assertInstanceOf(License::class, $license);
        $this->assertEquals(License::PROVIDER_PATREON, $license->getProvider());
    }
}
