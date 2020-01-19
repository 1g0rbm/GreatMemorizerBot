<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;

class QuizSettingsAction extends AbstractTelegramAction
{
    private Builder $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $this->builder->addLine([
            new InlineButton('Single', '/word_list_quiz'),
            new InlineButton('Regular', '/quiz_reminder')
        ]);

        $to->setText($command->getTextResponse());
        $to->setInlineKeyboard($this->builder->flush());

        return $to;
    }
}
