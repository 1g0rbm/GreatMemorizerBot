<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Event\Message;

use Ig0rbm\Memo\Entity\Telegram\Message\AnswerCallbackQuery;
use Symfony\Component\EventDispatcher\Event;

class CallbackQueryHandleEvent extends Event
{
    public const NAME = 'bot.message.callback_query_handle';

    private AnswerCallbackQuery $answerCallbackQuery;

    public function __construct(AnswerCallbackQuery $answerCallbackQuery)
    {
        $this->answerCallbackQuery = $answerCallbackQuery;
    }

    public function getAnswerCallbackQuery(): AnswerCallbackQuery
    {
        return $this->answerCallbackQuery;
    }
}
