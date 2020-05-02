<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Billing;

use RuntimeException;

use function sprintf;

class PatreonLicenseDeactivateException extends RuntimeException
{
    public static function notFoundPledgeByEmail(string $email): self
    {
        return new self(sprintf('There is no pledge for email %s', $email));
    }

    public static function pledgeDoesNotHaveAccount(int $pledgeId): self
    {
        return new self(sprintf('There is no account for pledge with id %s', $pledgeId));
    }

    public static function licenseNotFoundForAccount(int $accountId): self
    {
        return new self(sprintf('There is no license for account with id %s', $accountId));
    }
}
