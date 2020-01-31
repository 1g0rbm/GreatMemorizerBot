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
            new InlineButton(
                $this->translator->translate('button.inline.run_quiz', $to->getChatId()),
                '/word_list_quiz'
            ),
            new InlineButton(
                $this->translator->translate('button.inline.reminder', $to->getChatId()),
                '/quiz_reminder'
            )
        ]);

        $to->setText($this->translator->translate('messages.quiz_choose_action', $to->getChatId()));
        $to->setInlineKeyboard($this->builder->flush());

        return $to;
    }
}
