<?php

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ig0rbm\Memo\Entity\Telegram\Keyboard\ReplyKeyboardRemove;

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
     * @Assert\Type("string")
     *
     * @var string|null
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

    public function getChatId(): int
    {
        return $this->chatId;
    }

    public function setChatId(int $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getReplyMarkup(): Collection
    {
        return $this->replyMarkup;
    }

    public function getInlineKeyboard(): ?InlineKeyboard
    {
        return $this->replyMarkup->get(InlineKeyboard::KEY_NAME);
    }

    public function setReplyMarkup(Collection $replyMarkup): void
    {
        $this->replyMarkup = $replyMarkup;
    }

    public function setInlineKeyboard(InlineKeyboard $keyboard): void
    {
        $this->replyMarkup->set(InlineKeyboard::KEY_NAME, $keyboard);
    }

    public function setReplyKeyboardRemove(ReplyKeyboardRemove $replyKeyboardRemove): void
    {
        $this->replyMarkup->set(ReplyKeyboardRemove::KEY_NAME, $replyKeyboardRemove);
    }

    public function getReplyKeyboardRemove(): ?ReplyKeyboardRemove
    {
        return $this->replyMarkup->get(ReplyKeyboardRemove::KEY_NAME);
    }
}
