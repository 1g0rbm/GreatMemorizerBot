<?php

namespace Ig0rbm\Memo\Service;

use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Telegram\Message\ChatRepository;
use Ig0rbm\Memo\Repository\Translation\DirectionRepository;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;

class InitializeAccountService
{
    private ChatRepository $chatRepository;

    private AccountRepository $accountRepository;

    private DirectionRepository $directionRepository;

    private EntityFlusher $flusher;

    public function __construct(
        ChatRepository $chatRepository,
        AccountRepository $accountRepository,
        DirectionRepository $directionRepository,
        EntityFlusher $flusher
    ) {
        $this->chatRepository      = $chatRepository;
        $this->accountRepository   = $accountRepository;
        $this->directionRepository = $directionRepository;
        $this->flusher             = $flusher;
    }

    /**
     * @throws ORMException
     */
    public function initialize(Chat $chat): Account
    {
        $account = $this->accountRepository->findOneByChat($chat);
        if ($account) {
            return $account;
        }

        $chat = $this->chatRepository->findChatById($chat->getId()) ?? $chat;

        $account = new Account();
        $account->setChat($chat);
        $account->setDirection($this->directionRepository->getDefaultDirection());

        $this->accountRepository->addAccount($account);

        $this->flusher->flush();

        return $account;
    }
}
