<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception;

use RuntimeException;

class AccountNotFoundException extends RuntimeException
{
    public static function byAccountId(int $accountId): self
    {
        return new self(sprintf('There is no account with id = "%d"', $accountId));
    }
}
