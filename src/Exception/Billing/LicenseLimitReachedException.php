<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Billing;

use RuntimeException;

class LicenseLimitReachedException extends RuntimeException
{
    public static function forQuiz(): self
    {
        return new self('messages.errors.quiz_limit_reached');
    }
}
