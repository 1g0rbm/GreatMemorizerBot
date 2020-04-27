<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Billing;

use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Repository\Billing\Patreon\PledgeRepository;

class PatreonLicenseDeactivator
{
    private PledgeRepository $pledgeRepository;

    private LicenseRepository $licenseRepository;

    public function __construct(PledgeRepository $pledgeRepository, LicenseRepository $licenseRepository)
    {
        $this->pledgeRepository  = $pledgeRepository;
        $this->licenseRepository = $licenseRepository;
    }

    public function deactivate(string $email): void
    {

    }
}
