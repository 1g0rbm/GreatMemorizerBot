<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\Quiz\QuizReminderRepository;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;

class EditQuizReminderAction extends AbstractTelegramAction
{
    private QuizReminderRepository $quizReminderRepository;

    private Builder $builder;

    public function __construct(QuizReminderRepository $quizReminderRepository, Builder $builder)
    {
        $this->quizReminderRepository = $quizReminderRepository;
        $this->builder                = $builder;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());
        $to->setText($command->getTextResponse());

        if ($messageFrom->getCallbackQuery()) {
            $this->quizReminderRepository->deleteReminderByChatAndTime(
                $messageFrom->getChat(),
                $messageFrom->getCallbackQuery()->getData()->getText()
            );
        }

        $reminders = $this->quizReminderRepository->findAllRemindersByChat($messageFrom->getChat());
        if (empty($reminders)) {
            $to->setText($command->getTextResponse());

            return $to;
        }

        foreach ($reminders as $reminder) {
            $this->builder->addLine([new InlineButton($reminder->getTime(), $reminder->getTime())]);
        }

        $to->setText('🤖 press button to delete reminder');
        $to->setInlineKeyboard($this->builder->flush());

        return $to;
    }
}
