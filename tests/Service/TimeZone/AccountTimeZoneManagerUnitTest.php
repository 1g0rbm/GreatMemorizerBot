<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\TimeZone;

use GuzzleHttp\Exception\GuzzleException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\TimeZone\TimeZone;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\TimeZone\AccountTimeZoneManager;
use Ig0rbm\Memo\Service\TimeZone\ApiService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AccountTimeZoneManagerUnitTest extends TestCase
{
    private AccountTimeZoneManager $service;

    /** @var ApiService|MockObject */
    private ApiService $api;

    /** @var AccountRepository|MockObject */
    private AccountRepository $accountRepository;

    /** @var EntityFlusher|MockObject */
    private EntityFlusher $flusher;

    public function setUp(): void
    {
        parent::setUp();

        $this->api               = $this->createMock(ApiService::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->flusher           = $this->createMock(EntityFlusher::class);

        $this->service = new AccountTimeZoneManager($this->api, $this->accountRepository, $this->flusher);
    }

    /**
     * @throws GuzzleException
     */
    public function testSetTimeZoneForChatReturnAccountWithTimeZone(): void
    {
        $timeZoneValue = 'Asia/Barnaul';
        $lat           = 1.1;
        $lng           = 2.3;

        $chat = new Chat();
        $chat->setId(1);

        $timeZone = new TimeZone();
        $timeZone->setZoneName($timeZoneValue);

        $account = new Account();

        $this->accountRepository->expects($this->once())
            ->method('getOneByChatId')
            ->with($chat->getId())
            ->willReturn($account);

        $this->api->expects($this->once())
            ->method('getTimeZone')
            ->with($lat, $lng)
            ->willReturn($timeZone);

        $this->flusher->expects($this->once())->method('flush');

        $this->assertNull($account->getTimeZone());

        $this->service->setTimeZoneForChat($chat, $lat, $lng);

        $this->assertEquals($timeZoneValue, $account->getTimeZone());
    }
}
