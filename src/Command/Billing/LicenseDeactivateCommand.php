<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Command\Billing;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

class LicenseDeactivateCommand extends Command
{
    private LicenseRepository $licenseRepository;

    private EntityFlusher $flusher;

    private LoggerInterface $logger;

    public function __construct(
        LicenseRepository $licenseRepository,
        EntityFlusher $flusher,
        LoggerInterface $logger
    ) {
        parent::__construct(null);

        $this->licenseRepository = $licenseRepository;
        $this->flusher           = $flusher;
        $this->logger            = $logger;
    }

    public function configure(): void
    {
        $this->setName('memo:billing:license_deactivate')
            ->setDescription('Deactivate expired license');
    }

    /**
     * @throws NonUniqueResultException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $licenses = $this->licenseRepository->findActiveExpiredLicenses();

        foreach ($licenses as $license) {
            $license->setIsActive(false);

            $this->logger->notice(sprintf('[BILLING] LICENSE %d DEACTIVATE', $license->getId()));

            $licenseForActivate = $this->licenseRepository->findFutureInactiveLicenseByAccount(
                $license->getAccount(),
                $license->getDateEnd()
            );

            if ($licenseForActivate) {
                $licenseForActivate->setIsActive(true);
                $this->logger->notice(sprintf('[BILLING] LICENSE %d ACTIVATE', $licenseForActivate->getId()));
            }
        }

        $this->flusher->flush();

        return 0;
    }
}
