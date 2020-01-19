<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Telegram;

use RuntimeException;

class LocationException extends RuntimeException
{
    public static function becauseThereIsNoLocationInMessage(): self
    {
        return new self('There is no Location in Message');
    }
}
