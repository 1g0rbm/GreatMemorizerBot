<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\AccountRepository;

class CloseSettingsAction extends AbstractTelegramAction
{
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());
        $to->setText($command->getTextResponse());

        $account = $this->accountRepository->getOneByChatId($messageFrom->getChat()->getId());
        $account->setNeedKeyboardUpdate(true);

        return $to;
    }
}
