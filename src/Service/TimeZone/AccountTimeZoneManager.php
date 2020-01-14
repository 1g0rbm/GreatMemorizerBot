<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\TimeZone;

use GuzzleHttp\Exception\GuzzleException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\EntityFlusher;

class AccountTimeZoneManager
{
    private ApiService $api;

    private AccountRepository $accountRepository;

    private EntityFlusher $flusher;

    public function __construct(ApiService $api, AccountRepository $accountRepository, EntityFlusher $flusher)
    {
        $this->api               = $api;
        $this->accountRepository = $accountRepository;
        $this->flusher           = $flusher;
    }

    /**
     * @throws GuzzleException
     */
    public function setTimeZoneForChat(Chat $chat, float $lat, float $lng): Account
    {
        $account  = $this->accountRepository->getOneByChatId($chat->getId());
        $timeZone = $this->api->getTimeZone($lat, $lng);

        $account->setTimeZone($timeZone->getZoneName());
        $account->setNeedKeyboardUpdate(true);

        $this->flusher->flush();

        return $account;
    }
}
