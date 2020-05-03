<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Billing;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Billing\License;
use Ig0rbm\Memo\Entity\Billing\Patreon\Pledge;
use Ig0rbm\Memo\Exception\Billing\PatreonLicenseDeactivateException;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Repository\Billing\Patreon\PledgeRepository;
use Ig0rbm\Memo\Service\Billing\PatreonLicenseDeactivator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class PatreonLicenseDeactivatorUnitTest extends TestCase
{
    private PatreonLicenseDeactivator $service;

    /** @var PledgeRepository|MockObject */
    private PledgeRepository $pledgeRepository;

    /** @var LicenseRepository|MockObject */
    private LicenseRepository $licenseRepository;

    /** @var EntityManagerInterface|MockObject */
    private $em;

    public function setUp(): void
    {
        parent::setUp();

        $this->pledgeRepository  = $this->createMock(PledgeRepository::class);
        $this->licenseRepository = $this->createMock(LicenseRepository::class);
        $this->em                = $this->createMock(EntityManagerInterface::class);

        $this->service = new PatreonLicenseDeactivator($this->pledgeRepository, $this->licenseRepository, $this->em);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testDeactivateDoDeactivatePatreonLicense(): void
    {
        $email   = 'test@email.com';
        $account = new Account();
        $pledge  = new Pledge();
        $pledge->setId(1);
        $pledge->setAccount($account);

        $sourceLicense = new License(
            $account,
            new DateTimeImmutable(),
            new DateTimeImmutable('+ 5 years'),
            License::PROVIDER_PATREON
        );

        $this->pledgeRepository->expects($this->once())
            ->method('findOneByEmail')
            ->with($email)
            ->willReturn($pledge);

        $this->licenseRepository->expects($this->once())
            ->method('findActiveLicenseByAccountAndProvider')
            ->with($account, License::PROVIDER_PATREON)
            ->willReturn($sourceLicense);

        $this->em->expects($this->once())
            ->method('remove')
            ->with($pledge);

        $this->em->expects($this->once())
            ->method('flush');

        $licensee = $this->service->deactivate($email);

        $this->assertEquals(
            (new DateTimeImmutable('last day of this month'))->format('d-m-y'),
            $licensee->getDateEnd()->format('d-m-y')
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testDeactivateThrowExceptionIfThereIsNoPledgeForEmail(): void
    {
        $email = 'test@email.com';

        $this->pledgeRepository->expects($this->once())
            ->method('findOneByEmail')
            ->with($email)
            ->willReturn(null);

        $this->expectException(PatreonLicenseDeactivateException::class);

        $this->service->deactivate($email);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testDeactivateThrowExceptionIfThereIsNoAccountForPledge(): void
    {
        $email  = 'test@email.com';
        $pledge = new Pledge();
        $pledge->setId(1);

        $this->pledgeRepository->expects($this->once())
            ->method('findOneByEmail')
            ->with($email)
            ->willReturn($pledge);

        $this->expectException(PatreonLicenseDeactivateException::class);

        $this->service->deactivate($email);
    }
}
