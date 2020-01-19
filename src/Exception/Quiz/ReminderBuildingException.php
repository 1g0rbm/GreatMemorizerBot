<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Quiz;

use RuntimeException;

class ReminderBuildingException extends RuntimeException
{
    public static function becauseThereIsNoTimeZoneForChat(int $chatId): self
    {
        return new self(sprintf('There is no TimeZone for chat with id %d', $chatId));
    }
}
