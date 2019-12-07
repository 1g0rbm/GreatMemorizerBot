<?php

namespace Ig0rbm\Memo\Exception\Telegram;

use RuntimeException;

use function implode;
use function sprintf;

class MessageBuilderException extends RuntimeException
{
    public static function becauseIncorrectModifier(string $modifier, array $available): self
    {
        $message = sprintf(
            'Available modifiers: %s. Passed modifier: %s',
            implode(', ', $available),
            $modifier
        );

        return new self($message);
    }
}
