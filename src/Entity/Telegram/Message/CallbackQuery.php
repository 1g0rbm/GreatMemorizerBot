<?php

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Symfony\Component\Validator\Constraints as Assert;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;

class CallbackQuery
{
    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Type("integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @TelegramMessageAssert\From
     *
     * @var From
     */
    private $from;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    private $chatInstance;

    /**
     * @Assert\Type("string")
     *
     * @var string
     */
    private $data;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return From
     */
    public function getFrom(): From
    {
        return $this->from;
    }

    /**
     * @param From $from
     */
    public function setFrom(From $from): void
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getChatInstance(): string
    {
        return $this->chatInstance;
    }

    /**
     * @param string $chatInstance
     */
    public function setChatInstance(string $chatInstance): void
    {
        $this->chatInstance = $chatInstance;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data): void
    {
        $this->data = $data;
    }
}
