<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\Billing\PatreonLicenseActivator;
use Throwable;

class MatchPatreonEmailAction extends AbstractTelegramAction
{
    private AccountRepository $accountRepository;

    private PatreonLicenseActivator $licenseActivator;

    public function __construct(
        AccountRepository $accountRepository,
        PatreonLicenseActivator $licenseActivator
    ) {
        $this->accountRepository = $accountRepository;
        $this->licenseActivator  = $licenseActivator;
    }

    /**
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $account = $this->accountRepository->getOneByChatId($to->getChatId());
        $license = $this->licenseActivator->activate($account, $messageFrom->getText()->getText());

        $license ?
            $to->setText($this->translator->translate('messages.patreon.license_activated', $to->getChatId())) :
            $to->setText($this->translator->translate('messages.patreon.license_not_activated', $to->getChatId()));

        return $to;
    }
}
