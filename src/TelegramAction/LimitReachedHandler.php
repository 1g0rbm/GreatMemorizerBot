<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\Telegram\Message\UrlButton;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;

class LimitReachedHandler extends AbstractTelegramAction
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
        $to->setText($this->translator->translate($messageFrom->getText()->getText(), $to->getChatId()));

        $this->builder->addLine([
            new UrlButton(
                $this->translator->translate('button.inline.patreon_subscription', $to->getChatId()),
                'http://patreon.com/1g0rbm'
            ),
        ]);

        $to->setInlineKeyboard($this->builder->flush());

        return $to;
    }
}
