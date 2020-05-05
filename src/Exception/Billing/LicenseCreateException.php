<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Billing;

use RuntimeException;

use function sprintf;

class LicenseCreateException extends RuntimeException
{
    public static function invalidProvider(string $provider): self
    {
        return new self(sprintf('Invalid provider "%s"', $provider));
    }
}
