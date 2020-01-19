<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use DateTime;
use DateTimeZone;
use Exception;
use Ig0rbm\Memo\Entity\Quiz\QuizReminder;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Exception\Quiz\ReminderBuildingException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Quiz\QuizReminderRepository;
use Ig0rbm\Memo\Service\EntityFlusher;

class ReminderBuilder
{
    private QuizReminderRepository $reminderRepository;

    private EntityFlusher $flusher;
    /**
     * @var AccountRepository
     */
    private AccountRepository $accountRepository;

    public function __construct(
        QuizReminderRepository $reminderRepository,
        AccountRepository $accountRepository,
        EntityFlusher $flusher
    ) {
        $this->reminderRepository = $reminderRepository;
        $this->accountRepository  = $accountRepository;
        $this->flusher            = $flusher;
    }

    /**
     * @throws Exception
     */
    public function build(Chat $chat, string $time): QuizReminder
    {
        $reminder = $this->reminderRepository->findReminderByChatAndTime($chat, $time);
        if ($reminder) {
            return $reminder;
        }

        $account = $this->accountRepository->getOneByChatId($chat->getId());
        if (!$account->getTimeZone()) {
            throw ReminderBuildingException::becauseThereIsNoTimeZoneForChat($chat->getId());
        }

        $dt = new DateTime($time, new DateTimeZone($account->getTimeZone()));
        $dt->setTimezone(new DateTimeZone('UTC'));

        $reminder = new QuizReminder();
        $reminder->setChat($chat);
        $reminder->setStatus(QuizReminder::TYPE_ENABLE);
        $reminder->setTime($dt->format('H:i'));

        $this->reminderRepository->addQuizReminder($reminder);
        $this->flusher->flush();

        return $reminder;
    }
}
