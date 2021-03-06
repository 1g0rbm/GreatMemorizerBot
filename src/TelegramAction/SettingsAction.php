<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\Telegram\Message\ReplyButton;
use Ig0rbm\Memo\Service\Telegram\ReplyKeyboard\Builder;

class SettingsAction extends AbstractTelegramAction
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
        $to->setText($this->translator->translate($command->getTextResponse(), $to->getChatId()));

        $this->builder->addLine([
            new ReplyButton($this->translator->translate('button.menu.settings.timezone', $to->getChatId())),
            new ReplyButton($this->translator->translate('button.menu.settings.reminders', $to->getChatId())),
            new ReplyButton($this->translator->translate('button.menu.settings.list', $to->getChatId())),
        ]);

        $this->builder->addLine([
            new ReplyButton($this->translator->translate('button.menu.settings.locale', $to->getChatId()))
        ]);

        $this->builder->addLine([
            new ReplyButton($this->translator->translate('button.menu.settings.close', $to->getChatId()))
        ]);

        $to->setReplyKeyboard($this->builder->flush());

        return $to;
    }
}
