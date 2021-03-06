<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\Translation\DirectionRepository;
use Ig0rbm\Memo\Service\Translation\DirectionSwitcher;

class ChangeDirectionAction extends AbstractTelegramAction
{
    private DirectionRepository $repository;

    private DirectionSwitcher $switcher;

    public function __construct(DirectionRepository $repository, DirectionSwitcher $switcher)
    {
        $this->repository = $repository;
        $this->switcher   = $switcher;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $response = new MessageTo();
        $response->setChatId($messageFrom->getChat()->getId());

        [$from, $to] = explode(
            '-',
            str_replace(['🇷🇺🇬🇧   ', '🇬🇧🇷🇺   '], '', $messageFrom->getText()->getText())
        );

        $direction = $this->repository->findByFromAndTo($from, $to);
        if ($direction === null) {
            $response->setText('Wrong direction');
        }

        $this->switcher->switch($messageFrom->getChat(), $direction);

        $response->setText($this->translator->translate(
            'messages.change_direction',
            $response->getChatId(),
            ['from' => $direction->getLangFrom(), 'to' => $direction->getLangTo()]
        ));

        return $response;
    }
}
