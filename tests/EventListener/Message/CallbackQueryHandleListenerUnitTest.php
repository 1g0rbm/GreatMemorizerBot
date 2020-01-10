<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\EventListener\Message;

use Ig0rbm\Memo\Entity\Telegram\Message\AnswerCallbackQuery;
use Ig0rbm\Memo\Entity\Telegram\Message\CallbackQuery;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Event\Message\CallbackQueryHandleEvent;
use Ig0rbm\Memo\EventListener\Message\CallbackQueryHandleListener;
use Ig0rbm\Memo\Service\Telegram\TelegramApiService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CallbackQueryHandleListenerUnitTest extends TestCase
{
    private CallbackQueryHandleListener $service;

    /** @var TelegramApiService|MockObject */
    private TelegramApiService $telegramApiService;

    public function setUp(): void
    {
        parent::setUp();

        $this->telegramApiService = $this->createMock(TelegramApiService::class);

        $this->service = new CallbackQueryHandleListener($this->telegramApiService);
    }

    public function testOnCallbackQueryHandleSendCallbackAnswer(): void
    {
        $callback = new CallbackQuery();
        $callback->setId(11);

        $message = new MessageFrom();
        $message->setCallbackQuery($callback);

        $this->telegramApiService->expects($this->once())
            ->method('answerCallbackQuery')
            ->with(AnswerCallbackQuery::createWithId($callback->getId()));

        $this->service->onCallbackQueryHandle(new CallbackQueryHandleEvent($message));
    }

    public function testOnCallbackQueryHandleDontCallTelegramApiService(): void
    {
        $this->telegramApiService->expects($this->never())
            ->method('answerCallbackQuery');

        $this->service->onCallbackQueryHandle(new CallbackQueryHandleEvent(new MessageFrom()));
    }
}
