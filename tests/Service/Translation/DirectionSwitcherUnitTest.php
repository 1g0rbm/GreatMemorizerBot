<?php

namespace Ig0rbm\Memo\Tests\Service\Translation;

use Faker\Factory;
use Faker\Generator;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Exception\Translation\DirectionSwitchException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Repository\Translation\DirectionRepository;
use Ig0rbm\Memo\Service\Translation\DirectionSwitcher;

class DirectionSwitcherUnitTest extends TestCase
{
    /** @var DirectionSwitcher */
    private $service;

    /** @var Generator  */
    private $faker;

    /** @var DirectionRepository|MockObject */
    private $directionRepository;

    /** @var AccountRepository|MockObject */
    private $accountRepository;

    /** @var EntityFlusher|MockObject */
    private $flusher;

    public function setUp(): void
    {
        parent::setUp();

        $this->directionRepository = $this->createMock(DirectionRepository::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->flusher = $this->createMock(EntityFlusher::class);

        $this->faker = Factory::create();

        $this->service = new DirectionSwitcher($this->directionRepository, $this->accountRepository, $this->flusher);
    }

    public function testSwitchThrowExceptionIfThereIsNoAccount(): void
    {
        $chat = $this->getChat();
        $directionId = 1;

        $this->accountRepository->expects($this->once())
            ->method('findOneByChat')
            ->with($chat)
            ->willReturn(null);

        $this->directionRepository->expects($this->never())
            ->method('find');

        $this->flusher->expects($this->never())
            ->method('flush');

        $this->expectException(DirectionSwitchException::class);

        $this->service->switch($chat, $directionId);
    }

    public function testSwitchThrowExceptionIfThereIsNoDirection(): void
    {
        $chat = $this->getChat();
        $directionId = 1;

        $this->accountRepository->expects($this->once())
            ->method('findOneByChat')
            ->with($chat)
            ->willReturn($this->getAccount());

        $this->directionRepository->expects($this->once())
            ->method('find')
            ->with($directionId)
            ->willReturn(null);

        $this->flusher->expects($this->never())
            ->method('flush');

        $this->expectException(DirectionSwitchException::class);

        $this->service->switch($chat, $directionId);
    }

    public function testSwitchDirectionAndFlush(): void
    {
        $chat              = $this->getChat();
        $account           = $this->getAccount();
        $directionId       = 1;
        $expectedDirection = new Direction();
        $expectedDirection->setId($directionId);

        $this->accountRepository->expects($this->once())
            ->method('findOneByChat')
            ->with($chat)
            ->willReturn($account);

        $this->directionRepository->expects($this->once())
            ->method('find')
            ->with($directionId)
            ->willReturn($expectedDirection);

        $this->flusher->expects($this->once())->method('flush');

        $this->assertNotEquals($expectedDirection, $account->getDirection());

        $direction = $this->service->switch($chat, $directionId);

        $this->assertSame($expectedDirection, $direction);
        $this->assertSame($expectedDirection, $account->getDirection());
    }

    private function getAccount(): Account
    {
        $account = new Account();
        $account->setChat($this->getChat());
        $account->setDirection(new Direction());

        return $account;
    }

    private function getChat(): Chat
    {
        $chat = new Chat();
        $chat->setId($this->faker->unique()->randomNumber(9));

        return $chat;
    }
}
