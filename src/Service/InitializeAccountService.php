<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service;

use DateTimeImmutable;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Billing\License;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Billing\LicenseRepository;
use Ig0rbm\Memo\Repository\Telegram\Message\ChatRepository;
use Ig0rbm\Memo\Repository\Translation\DirectionRepository;
use Throwable;

class InitializeAccountService
{
    private ChatRepository $chatRepository;

    private AccountRepository $accountRepository;

    private LicenseRepository $licenseRepository;

    private DirectionRepository $directionRepository;

    private EntityFlusher $flusher;

    public function __construct(
        ChatRepository $chatRepository,
        AccountRepository $accountRepository,
        LicenseRepository $licenseRepository,
        DirectionRepository $directionRepository,
        EntityFlusher $flusher
    ) {
        $this->chatRepository      = $chatRepository;
        $this->accountRepository   = $accountRepository;
        $this->licenseRepository   = $licenseRepository;
        $this->directionRepository = $directionRepository;
        $this->flusher             = $flusher;
    }

    /**
     * @throws ORMException
     * @throws Throwable
     */
    public function initialize(Chat $chat): Account
    {
        $account = $this->accountRepository->findOneByChat($chat);
        if ($account) {
            return $account;
        }

        $chat    = $this->chatRepository->findChatById($chat->getId()) ?? $chat;
        $account = Account::createNewFromChatAndDirection($chat, $this->directionRepository->getDefaultDirection());
        $license = new License(
            $account,
            new DateTimeImmutable(),
            new DateTimeImmutable(sprintf('+ %d months', License::DEFAULT_TERM)),
            License::PROVIDER_DEFAULT
        );

        $this->accountRepository->addAccount($account);
        $this->licenseRepository->addLicense($license);

        $this->flusher->flush();

        return $account;
    }
}
