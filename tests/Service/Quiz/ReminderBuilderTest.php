<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Quiz;

use DateTime;
use DateTimeZone;
use Exception;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Quiz\QuizReminder;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Exception\Quiz\ReminderBuildingException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Quiz\QuizReminderRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Quiz\ReminderBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReminderBuilderTest extends TestCase
{
    private ReminderBuilder $service;

    /** @var QuizReminderRepository|MockObject */
    private QuizReminderRepository $quizReminderRepository;

    /** @var AccountRepository|MockObject */
    private AccountRepository $accountRepository;

    /** @var EntityFlusher|MockObject */
    private EntityFlusher $flusher;

    public function setUp(): void
    {
        parent::setUp();

        $this->quizReminderRepository = $this->createMock(QuizReminderRepository::class);
        $this->accountRepository      = $this->createMock(AccountRepository::class);
        $this->flusher                = $this->createMock(EntityFlusher::class);

        $this->service = new ReminderBuilder($this->quizReminderRepository, $this->accountRepository, $this->flusher);
    }

    /**
     * @throws Exception
     */
    public function testBuildReturnExistReminder(): void
    {
        $time     = '10:00';
        $reminder = new QuizReminder();
        $reminder->setTime($time);

        $chat = new Chat();

        $this->quizReminderRepository->expects($this->once())
            ->method('findReminderByChatAndTime')
            ->with($chat, $time)
            ->willReturn($reminder);

        $this->assertSame($reminder, $this->service->build($chat, $time));;
    }

    /**
     * @throws Exception
     */
    public function testBuildThrowExceptionIfThereIsNoTimezoneInAccount(): void
    {
        $time    = '10:00';
        $account = new Account();

        $chat = new Chat();
        $chat->setId(1);

        $this->quizReminderRepository->expects($this->once())
            ->method('findReminderByChatAndTime')
            ->with($chat, $time)
            ->willReturn(null);

        $this->accountRepository->expects($this->once())
            ->method('getOneByChatId')
            ->with($chat->getId())
            ->willReturn($account);

        $this->expectException(ReminderBuildingException::class);

        $this->service->build($chat, $time);
    }

    /**
     * @throws Exception
     */
    public function testBuildReturnNewReminder(): void
    {
        $time    = '10:00';
        $account = new Account();
        $account->setTimeZone('Asia/Barnaul');

        $chat = new Chat();
        $chat->setId(1);

        $this->quizReminderRepository->expects($this->once())
            ->method('findReminderByChatAndTime')
            ->with($chat, $time)
            ->willReturn(null);

        $this->accountRepository->expects($this->once())
            ->method('getOneByChatId')
            ->with($chat->getId())
            ->willReturn($account);

        $this->flusher->expects($this->once())->method('flush');

        $reminder = $this->service->build($chat, $time);

        $dt = new DateTime($time, new DateTimeZone($account->getTimeZone()));
        $dt->setTimezone(new DateTimeZone('UTC'));

        $this->assertEquals($dt->format('H:i'), $reminder->getTime());
        $this->assertEquals($chat, $reminder->getChat());
        $this->assertEquals(QuizReminder::TYPE_ENABLE, $reminder->getStatus());
    }
}
