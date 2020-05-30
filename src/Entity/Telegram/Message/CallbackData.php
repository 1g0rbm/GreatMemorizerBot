<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Telegram\Message;

class CallbackData
{
    private string $command;

    private array $parameters = [];

    public function __construct(string $command)
    {
        $this->command = $command;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setParameter(string $key, string $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function getParameter(string $key, ?string $defaultValue = null): ?string
    {
        if (!isset($this->parameters[$key])) {
            return $defaultValue;
        }

        return $this->parameters[$key];
    }

    public function getParametersString(): string
    {
        return implode(
            '&',
            array_map(
                static fn($key, $value) => sprintf('%s=%s', $key, $value),
                array_keys($this->parameters),
                $this->parameters
            )
        );
    }

    public function getCallbackData(): string
    {
        return sprintf('%s?%s', $this->command, $this->getParametersString());
    }
}
