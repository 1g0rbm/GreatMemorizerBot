<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Exception\Translation\DirectionSwitchException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Translation\DirectionRepository;
use Ig0rbm\Memo\Service\EntityFlusher;

class DirectionSwitcher
{
    private DirectionRepository $directionRepository;

    private AccountRepository $accountRepository;

    private EntityFlusher $flusher;

    public function __construct(
        DirectionRepository $directionRepository,
        AccountRepository $accountRepository,
        EntityFlusher $flusher
    ) {
        $this->directionRepository = $directionRepository;
        $this->accountRepository   = $accountRepository;
        $this->flusher             = $flusher;
    }

    public function switch(Chat $chat, Direction $direction): Direction
    {
        $account = $this->accountRepository->findOneByChat($chat);
        if ($account === null) {
            throw DirectionSwitchException::becauseNotFoundAccountForSwitch($chat->getId());
        }

        $account->setDirection($direction);
        $account->setNeedKeyboardUpdate(true);

        $this->flusher->flush();

        return $direction;
    }
}
