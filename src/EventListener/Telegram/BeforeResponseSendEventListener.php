<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\EventListener\Telegram;

use Ig0rbm\Memo\Entity\Telegram\Message\ReplyButton;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Event\Telegram\BeforeSendResponseEvent;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Telegram\ReplyKeyboard\Builder;
use Ig0rbm\Memo\Service\Telegram\TranslationService;

class BeforeResponseSendEventListener
{
    private AccountRepository $accountRepository;

    private Builder $builder;

    private EntityFlusher $flusher;

    private TranslationService $translator;

    public function __construct(
        AccountRepository $accountRepository,
        Builder $builder,
        EntityFlusher $flusher,
        TranslationService $translator
    ) {
        $this->accountRepository = $accountRepository;
        $this->builder           = $builder;
        $this->flusher           = $flusher;
        $this->translator        = $translator;
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

        $this->builder->addLine([new ReplyButton($this->getButtonByDirection($account->getDirection()))]);

        $this->builder->addLine([
            new ReplyButton($this->translator->translate('button.menu.list', $msgTo->getChatId()))
        ]);

        $this->builder->addLine([
            new ReplyButton($this->translator->translate('button.menu.settings_btn', $msgTo->getChatId()))
        ]);

        $msgTo->setReplyKeyboard($this->builder->flush());
    }

    private function getButtonByDirection(Direction $direction): string
    {
        if ($direction->getDirection() === str_replace('🇬🇧🇷🇺   ', '', Direction::getEnRu())) {
            return Direction::getRuEn();
        }

        return Direction::getEnRu();
    }
}
