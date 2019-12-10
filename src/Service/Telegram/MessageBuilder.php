<?php

namespace Ig0rbm\Memo\Service\Telegram;

use Ig0rbm\Memo\Exception\Telegram\MessageBuilderException;

use function in_array;

class MessageBuilder
{
    private string $string = '';

    public const BOLD   = '*%s*';
    public const NORMAL = '%s';

    private const AVAILABLE_MODIFIERS = [
        self::NORMAL,
        self::BOLD
    ];

    public function addLineBreak(): self
    {
        $this->string .= PHP_EOL;

        return $this;
    }

    public function appendLn(string $string, string $modifier = self::NORMAL): self
    {
        $this->append($string, $modifier)->addLineBreak();

        return $this;
    }

    public function append(string $string, string $modifier = self::NORMAL): self
    {
        if (!in_array($modifier,self::AVAILABLE_MODIFIERS)) {
            throw MessageBuilderException::becauseIncorrectModifier($modifier, self::AVAILABLE_MODIFIERS);
        }

        $this->string .= sprintf($modifier, $string);

        return $this;
    }

    public function flush(): string
    {
        $res = $this->string;
        $this->string = '';

        return $res;
    }
}
