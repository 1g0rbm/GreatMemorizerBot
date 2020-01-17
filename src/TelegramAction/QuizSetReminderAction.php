<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Exception;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Quiz\ReminderBuilder;

class QuizSetReminderAction extends AbstractTelegramAction
{
    private ReminderBuilder $reminderBuilder;

    public function __construct(ReminderBuilder $reminderBuilder)
    {
        $this->reminderBuilder = $reminderBuilder;
    }

    /**
     * @throws Exception
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $reminder = $this->reminderBuilder->build($messageFrom->getChat(), $messageFrom->getText()->getText());

        $to->setText(sprintf('Reminder was successfully set for time %s', $reminder->getTime()));

        return $to;
    }
}
