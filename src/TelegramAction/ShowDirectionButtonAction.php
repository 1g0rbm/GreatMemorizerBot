<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Repository\Translation\DirectionRepository;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;
use Ig0rbm\Memo\Service\Translation\DirectionSwitcher;

class ShowDirectionButtonAction extends AbstractTelegramAction
{
    /** @var DirectionRepository */
    private $directionRepository;

    /** @var DirectionSwitcher */
    private $directionSwitcher;

    /** @var Builder */
    private $builder;

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
            $direction = $this->directionSwitcher->switch($messageFrom->getChat(), $callback->getData());
            $to->setText(sprintf('Your direction now is %s', $direction->getDirection()));

            return $to;
        }

        $line       = [];
        $directions = $this->directionRepository->findAll();
        /** @var Direction $direction */
        foreach ($directions as $direction) {
            array_push($line, new InlineButton($direction->getDirection(), $direction->getId()));
        }

        $this->builder->addLine($line);

        $to->setInlineKeyboard($this->builder->flush());
        $to->setText(sprintf('%s %s', $command->getCommand(), $command->getTextResponse()));

        return $to;
    }
}
