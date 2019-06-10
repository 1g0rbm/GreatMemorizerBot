<?php

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Symfony\Component\Validator\Constraints as Assert;

class MessageTo
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("integer")
     *
     * @var int
     */
    private $chatId;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    private $text;

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return $this->chatId;
    }

    /**
     * @param int $chatId
     */
    public function setChatId(int $chatId): void
    {
        $this->chatId = $chatId;
    }

    /**
     * @return string
     */
    public function getText(): string
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