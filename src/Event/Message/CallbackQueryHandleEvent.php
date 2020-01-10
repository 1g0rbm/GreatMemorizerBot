<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Event\Message;

use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Symfony\Component\EventDispatcher\Event;

class CallbackQueryHandleEvent extends Event
{
    public const NAME = 'bot.message.callback_query_handle';

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
