<?php


namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Ig0rbm\HandyBag\HandyBag;
use Symfony\Component\Validator\Constraints as Assert;

class Text
{
    /**
     * @Assert\Type("string")
     * @Assert\Regex("#^/#")
     */
    private ?string $command = null;

    /**
     * @Assert\Type("string")
     */
    private ?string $text = null;

    private HandyBag $parameters;

    public function __construct()
    {
        $this->parameters = new HandyBag();
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getParameters(): HandyBag
    {
        return $this->parameters;
    }

    public function setParameters(HandyBag $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function setParameter(string $key, $value): void
    {
        $this->parameters->set($key, $value);
    }
}
