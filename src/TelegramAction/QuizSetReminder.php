<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;

class QuizSetReminder extends AbstractTelegramAction
{
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());
        $to->setText('QUIZ SET REMINDER');

        return $to;
    }
}
