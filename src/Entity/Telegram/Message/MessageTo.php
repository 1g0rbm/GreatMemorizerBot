<?php

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @Assert\NotBlank
     *
     * @var Collection
     */
    private $replyMarkup;

    public function __construct()
    {
        $this->replyMarkup = new ArrayCollection();
    }

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

    /**
     * @return Collection
     */
    public function getReplyMarkup(): Collection
    {
        return $this->replyMarkup;
    }

    public function getInlineKeyboard(): ?InlineKeyboard
    {
        return $this->replyMarkup->get(InlineKeyboard::KEY_NAME);
    }

    /**
     * @param Collection $replyMarkup
     */
    public function setReplyMarkup(Collection $replyMarkup): void
    {
        $this->replyMarkup = $replyMarkup;
    }

    public function setInlineKeyboard(InlineKeyboard $keyboard): void
    {
        $this->replyMarkup->set(InlineKeyboard::KEY_NAME, $keyboard);
    }
}
