<?php


namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Symfony\Component\Validator\Constraints as Assert;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message as TelegramMessageAssert;

class MessageFrom
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("integer");
     *
     * @var integer
     */
    private $messageId;

    /**
     * @Assert\NotBlank
     * @TelegramMessageAssert\From
     *
     * @var From
     */
    private $from;

    /**
     * @Assert\NotBlank
     * @TelegramMessageAssert\Chat
     *
     * @var Chat
     */
    private $chat;

    /**
     * @Assert\NotBlank
     * @Assert\Type("integer")
     *
     * @var int
     */
    private $date;

    /**
     * @Assert\NotBlank
     * @TelegramMessageAssert\Text
     *
     * @var Text
     */
    private $text;

    /**
     * @TelegramMessageAssert\MessageFrom
     *
     * @var MessageFrom
     */
    private $reply;

    /**
     * @TelegramMessageAssert\CallbackQuery
     *
     * @var CallbackQuery|null
     */
    private $callbackQuery;

    /**
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->messageId;
    }

    /**
     * @param int $messageId
     */
    public function setMessageId(int $messageId): void
    {
        $this->messageId = $messageId;
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
     * @return Chat
     */
    public function getChat(): Chat
    {
        return $this->chat;
    }

    /**
     * @param Chat $chat
     */
    public function setChat(Chat $chat): void
    {
        $this->chat = $chat;
    }

    /**
     * @return int
     */
    public function getDate(): int
    {
        return $this->date;
    }

    /**
     * @param int $date
     */
    public function setDate(int $date): void
    {
        $this->date = $date;
    }

    /**
     * @return Text
     */
    public function getText(): Text
    {
        return $this->text;
    }

    /**
     * @param Text $text
     */
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

        return $this->callbackQuery->getCommand();
    }
}
