<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\EventListener\Message;

use Ig0rbm\Memo\Entity\Telegram\Message\AnswerCallbackQuery;
use Ig0rbm\Memo\Event\Message\CallbackQueryHandleEvent;
use Ig0rbm\Memo\Service\Telegram\TelegramApiService;

class CallbackQueryHandleListener
{
    private TelegramApiService $telegramApiService;

    public function __construct(TelegramApiService $telegramApiService)
    {
        $this->telegramApiService = $telegramApiService;
    }

    public function onCallbackQueryHandle(CallbackQueryHandleEvent $event): void
    {
        if ($event->getFrom()->getCallbackQuery() === null) {
            return;
        }

        $this->telegramApiService->answerCallbackQuery(
            AnswerCallbackQuery::createWithId($event->getFrom()->getCallbackQuery()->getId())
        );
    }
}
