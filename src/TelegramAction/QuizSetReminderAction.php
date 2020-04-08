<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Exception;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\Billing\Limiter\ReminderLicenseLimiter;
use Ig0rbm\Memo\Service\Quiz\ReminderBuilder;

class QuizSetReminderAction extends AbstractTelegramAction
{
    private ReminderBuilder $reminderBuilder;

    private AccountRepository $accountRepository;

    private ReminderLicenseLimiter $limiter;

    public function __construct(
        ReminderBuilder $reminderBuilder,
        AccountRepository $accountRepository,
        ReminderLicenseLimiter $limiter
    ) {
        $this->reminderBuilder   = $reminderBuilder;
        $this->limiter           = $limiter;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @throws Exception
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $account = $this->accountRepository->getOneByChatId($to->getChatId());
        if ($this->limiter->isLimitReached($account)) {
            $to->setText(
                $this->translator->translate('messages.license.reminder_limit_reached', $to->getChatId())
            );

            return $to;
        }

        $this->reminderBuilder->build($messageFrom->getChat(), $messageFrom->getText()->getText());

        $to->setText(
            $this->translator->translate(
                'messages.reminder_successfully_set',
                $to->getChatId(),
                ['time' => $messageFrom->getText()->getText()]
            )
        );

        return $to;
    }
}
