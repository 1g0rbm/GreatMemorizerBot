<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\EventListener\Telegram;

use Ig0rbm\Memo\Entity\Telegram\Message\ReplyButton;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Event\Telegram\BeforeSendResponseEvent;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Telegram\ReplyKeyboard\Builder;

class BeforeResponseSendEventListener
{
    private AccountRepository $accountRepository;

    private Builder $builder;

    private EntityFlusher $flusher;

    public function __construct(AccountRepository $accountRepository, Builder $builder, EntityFlusher $flusher)
    {
        $this->accountRepository = $accountRepository;
        $this->builder           = $builder;
        $this->flusher           = $flusher;
    }

    public function onBeforeResponseSend(BeforeSendResponseEvent $event): void
    {
        $msgTo   = $event->getMessageTo();
        $account = $this->accountRepository->getOneByChatId($msgTo->getChatId());

        if (! $account->isNeedKeyboardUpdate()) {
            return;
        }

        $account->setNeedKeyboardUpdate(false);
        $this->flusher->flush();

        $this->builder->addLine([
            new ReplyButton(Direction::getRuEn()),
            new ReplyButton(Direction::getEnRu())
        ]);

        $msgTo->setReplyKeyboard($this->builder->flush());
    }
}
