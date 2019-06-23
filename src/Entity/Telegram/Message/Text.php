<?php


namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Symfony\Component\Validator\Constraints as Assert;

class Text
{
    /**
     * @Assert\Type("string")
     * @Assert\Regex("#^/#")
     *
     * @var string
     */
    private $command;

    /**
     * @Assert\Type("string")
     *
     * @var string
     */
    private $text;

    /**
     * @return null|string
     */
    public function getCommand(): ?string
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
     * @return null|string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}