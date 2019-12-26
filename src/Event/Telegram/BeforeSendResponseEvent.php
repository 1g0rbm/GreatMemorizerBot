<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Event\Telegram;

use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Symfony\Component\EventDispatcher\Event;

class BeforeSendResponseEvent extends Event
{
    public const NAME = 'bot.before_send_response';

    private MessageTo $messageTo;

    public function __construct(MessageTo $messageTo)
    {
        $this->messageTo = $messageTo;
    }

    public function getMessageTo(): MessageTo
    {
        return $this->messageTo;
    }
}
