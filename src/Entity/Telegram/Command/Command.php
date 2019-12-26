<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Telegram\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public const REGEXP = '#^/\w+#';
    public const DEFAULT_COMMAND_NAME = 'default';

    /**
     * @Assert\Regex("#^/#")
     * @Assert\NotBlank
     */
    private string $command;

    /**
     * @Assert\Type("string")
     */
    private string $textResponse;

    /**
     * @Assert\Type("string")
     */
    private string $actionClass;

    private Collection $aliases;

    public function __construct()
    {
        $this->aliases = new ArrayCollection();
    }

    public function isDefault(): bool
    {
        return $this->getCommand() === static::DEFAULT_COMMAND_NAME;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    public function getTextResponse(): string
    {
        return $this->textResponse;
    }

    public function setTextResponse(string $textResponse): void
    {
        $this->textResponse = $textResponse;
    }

    public function getActionClass(): string
    {
        return $this->actionClass;
    }

    public function setActionClass(string $actionClass): void
    {
        $this->actionClass = $actionClass;
    }

    /**
     * @return Collection|string[]
     */
    public function getAliases(): Collection
    {
        return $this->aliases;
    }

    /**
     * @param $aliases Collection|string[]
     */
    public function setAliases(Collection $aliases): void
    {
        $this->aliases = $aliases;
    }
}
