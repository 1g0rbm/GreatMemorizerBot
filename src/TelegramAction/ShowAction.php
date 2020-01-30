<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;
use Ig0rbm\Memo\Service\WordList\WordListShowService;

class ShowAction extends AbstractTelegramAction
{
    private WordListShowService $showService;

    private Builder $builder;

    public function __construct(WordListShowService $showService, Builder $builder)
    {
        $this->showService = $showService;
        $this->builder     = $builder;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $wordList = $this->showService->findByChat($messageFrom->getChat());
        $to->setText($wordList ?? $command->getTextResponse());

        $this->builder->addLine([
            new InlineButton($this->translator->translate(
                'button.inline.quiz',
                $to->getChatId()),
                '/quiz_settings'
            )
        ]);
        $to->setInlineKeyboard($this->builder->flush());

        return $to;
    }
}
