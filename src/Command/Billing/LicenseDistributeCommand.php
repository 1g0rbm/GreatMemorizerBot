<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Command\Billing;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Service\AccountLicenseService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function sprintf;

class LicenseDistributeCommand extends Command
{
    private AccountLicenseService $accountLicenseService;

    public function __construct(AccountLicenseService $accountLicenseService)
    {
        parent::__construct(null);

        $this->accountLicenseService = $accountLicenseService;
    }

    public function configure(): void
    {
        $this
            ->setName('memo:billing:license_distributor')
            ->setDescription('Get default free license for account')
            ->addOption(
                'id',
                null,
                InputOption::VALUE_REQUIRED,
                'Account id for creating license',
                null
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'Create license for all accounts'
            );
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '=========',
            '  START  ',
            '=========',
            '',
        ]);

//        $forAll    = $input->getOption('all');
        $accountId = $input->getOption('id') ? (int) $input->getOption('id') : null;

        $license = $this->accountLicenseService->createLicense($accountId);

        if ($license === null) {
            $output->writeln([
                sprintf('License did not create for account_id = %d', $accountId),
                '',
            ]);
        } else {
            $output->writeln([
                'License created: ',
                sprintf('Id: %d', $license->getId()),
                sprintf('End date: %s', $license->getDateEnd()->format('d-m-Y H:i:s')),
                sprintf('Account id: %d', $license->getAccount()->getId()),
                '',
            ]);
        }

        $output->writeln([
            '=========',
            '   END   ',
            '=========',
        ]);

        return 0;
    }
}
