<?php

namespace Ig0rbm\Memo\Exception\Translation;

use DomainException;

class DirectionSwitchException extends DomainException
{
    public static function becauseNotFoundAccountForSwitch(int $accountId): self
    {
        return new self(sprintf('There is no account for chat id %d in DB', $accountId));
    }

    public static function becauseNotFoundDirectionForSwitch(int $direction): self
    {
        return new self(sprintf('There is no direction for with %d in DB', $direction));
    }
}
