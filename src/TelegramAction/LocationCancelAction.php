<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\TimeZone\TimeZone;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;

class LocationCancelAction extends AbstractTelegramAction
{
    private AccountRepository $accountRepository;

    private MessageBuilder $builder;

    private EntityFlusher $flusher;

    public function __construct(AccountRepository $accountRepository, MessageBuilder $builder, EntityFlusher $flusher)
    {
        $this->accountRepository = $accountRepository;
        $this->builder           = $builder;
        $this->flusher           = $flusher;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $account = $this->accountRepository->getOneByChatId($messageFrom->getChat()->getId());

        $account->setTimeZone(TimeZone::DEFAULT);
        $account->setNeedKeyboardUpdate(true);

        $this->flusher->flush();

        $this->builder->appendLn($this->translator->translate('messages.timezone_set_utc', $to->getChatId()))
            ->appendLn('')
            ->append($this->translator->translate('messages.quiz_creating_instruction', $to->getChatId()));

        $to->setText($this->builder->flush());

        return $to;
    }
}
