<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Repository\Translation\DirectionRepository;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;
use Ig0rbm\Memo\Service\Translation\DirectionSwitcher;

use function array_map;

class ShowDirectionButtonAction extends AbstractTelegramAction
{
    private DirectionRepository $directionRepository;

    private DirectionSwitcher $directionSwitcher;

    private Builder $builder;

    public function __construct(
        DirectionRepository $directionRepository,
        DirectionSwitcher $directionSwitcher,
        Builder $builder
    ) {
        $this->directionRepository = $directionRepository;
        $this->directionSwitcher   = $directionSwitcher;
        $this->builder             = $builder;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $callback = $messageFrom->getCallbackQuery();
        if ($callback) {
            $direction        = $this->directionRepository->getDirectionById((int) $callback->getData()->getText());
            $changedDirection = $this->directionSwitcher->switch($messageFrom->getChat(), $direction);
            $to->setText(
                $this->translator->translate(
                    'messages.change_direction',
                    $to->getChatId(),
                    ['direction' => $changedDirection->getDirection()]
                )
            );

            return $to;
        }

        $line = array_map(
            fn(Direction $direction) => new InlineButton($direction->getDirection(), (string) $direction->getId()),
            $this->directionRepository->findAll()
        );

        $this->builder->addLine($line);

        $to->setInlineKeyboard($this->builder->flush());
        $to->setText(sprintf('%s %s', $command->getCommand(), $command->getTextResponse()));

        return $to;
    }
}
