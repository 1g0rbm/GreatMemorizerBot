<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;
use Symfony\Component\Validator\Constraints as Assert;

class CallbackQuery
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("integer")
     */
    private int $id;

    /**
     * @Assert\NotBlank
     * @TelegramMessageAssert\From
     */
    private From $from;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $chatInstance;

    /**
     * @Assert\Type("string")
     */
    private Text $data;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFrom(): From
    {
        return $this->from;
    }

    public function setFrom(From $from): void
    {
        $this->from = $from;
    }

    public function getChatInstance(): string
    {
        return $this->chatInstance;
    }

    public function setChatInstance(string $chatInstance): void
    {
        $this->chatInstance = $chatInstance;
    }

    public function getData(): Text
    {
        return $this->data;
    }

    public function setData(Text $data): void
    {
        $this->data = $data;
    }

    public function getCommand(): ?string
    {
        return $this->isDataIsCommand() ? $this->data : null;
    }

    public function isDataIsCommand(): bool
    {
        $matches = [];
        preg_match(Command::REGEXP, $this->data, $matches);

        return count($matches) >= 1;
    }
}
