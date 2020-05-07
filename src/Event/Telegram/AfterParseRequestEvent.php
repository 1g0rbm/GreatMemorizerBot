<?php


namespace Ig0rbm\Memo\Event\Telegram;

use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Symfony\Component\EventDispatcher\Event;

class AfterParseRequestEvent extends Event
{
    public const NAME = 'bot.after_parse_request';

    private MessageFrom $from;

    public function __construct(MessageFrom $from)
    {
        $this->from = $from;
    }

    public function getFrom(): MessageFrom
    {
        return $this->from;
    }
}
