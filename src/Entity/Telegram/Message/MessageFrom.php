<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;
use Symfony\Component\Validator\Constraints as Assert;

class MessageFrom
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("integer");
     */
    private int $messageId;

    /**
     * @Assert\NotBlank
     * @TelegramMessageAssert\From
     */
    private From $from;

    /**
     * @Assert\NotBlank
     * @TelegramMessageAssert\Chat
     */
    private Chat $chat;

    /**
     * @Assert\NotBlank
     * @Assert\Type("integer")
     */
    private int $date;

    /**
     * @Assert\NotBlank
     * @TelegramMessageAssert\Text
     */
    private Text $text;

    /**
     * @TelegramMessageAssert\MessageFrom
     */
    private ?MessageFrom $reply = null;

    /**
     * @TelegramMessageAssert\CallbackQuery
     */
    private ?CallbackQuery $callbackQuery = null;

    /**
     * @TelegramMessageAssert\Location
     */
    private ?Location $location = null;

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function setMessageId(int $messageId): void
    {
        $this->messageId = $messageId;
    }

    public function getFrom(): From
    {
        return $this->from;
    }

    public function setFrom(From $from): void
    {
        $this->from = $from;
    }

    public function getChat(): Chat
    {
        return $this->chat;
    }

    public function setChat(Chat $chat): void
    {
        $this->chat = $chat;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function setDate(int $date): void
    {
        $this->date = $date;
    }

    public function getText(): Text
    {
        return $this->text;
    }

    public function setText(Text $text): void
    {
        $this->text = $text;
    }

    public function getReply(): ?MessageFrom
    {
        return $this->reply;
    }

    public function setReply(MessageFrom $reply): void
    {
        $this->reply = $reply;
    }

    public function getCallbackQuery(): ?CallbackQuery
    {
        return $this->callbackQuery;
    }

    public function setCallbackQuery(?CallbackQuery $callbackQuery): void
    {
        $this->callbackQuery = $callbackQuery;
    }

    public function getCallbackCommand(): ?string
    {
        if ($this->callbackQuery === null) {
            return null;
        }

        return $this->callbackQuery->getData()->getCommand();
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }
}
