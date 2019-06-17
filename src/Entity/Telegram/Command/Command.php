<?php

namespace Ig0rbm\Memo\Entity\Telegram\Command;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public const DEFAULT_COMMAND_NAME = 'default';

    /**
     * @Assert\Regex("#^/#")
     * @Assert\NotBlank
     *
     * @var string
     */
    private $command;

    /**
     * @Assert\Type("string")
     *
     * @var string
     */
    private $textResponse;

    /**
     * @Assert\Type("string")
     *
     * @var string
     */
    private $actionClass;

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->getCommand() === static::DEFAULT_COMMAND_NAME;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @param string $command
     */
    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getTextResponse(): string
    {
        return $this->textResponse;
    }

    /**
     * @param string $textResponse
     */
    public function setTextResponse(string $textResponse): void
    {
        $this->textResponse = $textResponse;
    }

    /**
     * @return string
     */
    public function getActionClass(): string
    {
        return $this->actionClass;
    }

    /**
     * @param string $actionClass
     */
    public function setActionClass(string $actionClass): void
    {
        $this->actionClass = $actionClass;
    }
}