<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Telegram;

use RuntimeException;

use function sprintf;

class AccountException extends RuntimeException
{
    public static function becauseThereIsNotAccountForChat(int $chatId): self
    {
        return new self(sprintf('There is not account for ChatId = "%d"', $chatId));
    }
}
