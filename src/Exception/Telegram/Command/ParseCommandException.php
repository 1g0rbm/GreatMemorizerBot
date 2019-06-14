<?php

namespace Ig0rbm\Memo\Exception\Telegram\Command;

use Exception;

class ParseCommandException extends Exception
{
    public static function becauseInvalidCommandName(string $name): self
    {
        return new self(sprintf('Invalid telegram command name: "%s"', $name), 500);
    }

    public static function becauseNoNecessaryParam(string $param): self
    {
        return new self(sprintf('There is no param: "%s"', $param), 500);
    }
}