<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;

class QuizReminderAction extends AbstractTelegramAction
{
    private AccountRepository $accountRepository;

    private TimezoneAction $timezoneAction;

    private MessageBuilder $builder;

    public function __construct(
        AccountRepository $accountRepository,
        TimezoneAction $timezoneAction,
        MessageBuilder $builder
    ) {
        $this->accountRepository = $accountRepository;
        $this->timezoneAction    = $timezoneAction;
        $this->builder           = $builder;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
       $to = new MessageTo();
       $to->setChatId($messageFrom->getChat()->getId());

       $account = $this->accountRepository->getOneByChatId($messageFrom->getChat()->getId());
       if (!$account->getTimeZone()) {
           $messageFrom->getText()->setCommand('/time_zone');

           return $this->timezoneAction->run($messageFrom, $command);
       }

        $this->builder->appendLn(sprintf('Your TimeZone is "%s"', $account->getTimeZone()))
            ->appendLn('')
            ->append('For creating regular quiz you must write message by pattern ')
            ->append('HH:MM', MessageBuilder::BOLD)
            ->appendLn('.')
            ->append('For example ')
            ->appendLn('12:45', MessageBuilder::BOLD);

       $to->setText($this->builder->flush());

       return $to;
    }
}
