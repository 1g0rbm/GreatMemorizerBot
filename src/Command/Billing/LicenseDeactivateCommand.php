<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Command\Billing;

use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LicenseDeactivateCommand extends Command
{
    private LicenseRepository $licenseRepository;

    private EntityFlusher $flusher;

    public function __construct(LicenseRepository $licenseRepository, EntityFlusher $flusher)
    {
        parent::__construct(null);

        $this->licenseRepository = $licenseRepository;
        $this->flusher = $flusher;
    }

    public function configure(): void
    {
        $this->setName('memo:billing:license_deactivate')
            ->setDescription('Deactivate expired license');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $licenses = $this->licenseRepository->findActiveExpiredLicenses();

        var_dump(count($licenses));

        foreach ($licenses as $license) {
            $license->setIsActive(false);
        }

        $this->flusher->flush();

        return 0;
    }
}
