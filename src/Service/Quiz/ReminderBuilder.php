<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Exception;
use Ig0rbm\Memo\Entity\Quiz\QuizReminder;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\TimeZone\TimeZone;
use Ig0rbm\Memo\Exception\Quiz\ReminderBuildingException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Quiz\QuizReminderRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\TimeZoneConverter;

class ReminderBuilder
{
    private QuizReminderRepository $reminderRepository;

    private AccountRepository $accountRepository;

    private TimeZoneConverter $timeZoneConverter;

    private EntityFlusher $flusher;

    public function __construct(
        QuizReminderRepository $reminderRepository,
        AccountRepository $accountRepository,
        TimeZoneConverter $timeZoneConverter,
        EntityFlusher $flusher
    ) {
        $this->reminderRepository = $reminderRepository;
        $this->accountRepository  = $accountRepository;
        $this->timeZoneConverter  = $timeZoneConverter;
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

        $dt = $this->timeZoneConverter->convert($time, $account->getTimeZone(), TimeZone::DEFAULT);

        $reminder = new QuizReminder();
        $reminder->setChat($chat);
        $reminder->setStatus(QuizReminder::TYPE_ENABLE);
        $reminder->setTime($dt->format('H:i'));

        $this->reminderRepository->addQuizReminder($reminder);
        $this->flusher->flush();

        return $reminder;
    }
}
