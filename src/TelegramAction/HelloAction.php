<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Telegram\TelegramApiService;

class HelloAction extends AbstractTelegramAction
{
    /** @var TelegramApiService */
    private $api;

    public function __construct(TelegramApiService $api)
    {
        $this->api = $api;
    }

    public function run(MessageFrom $from, Command $command): void
    {
        $messageTo = new MessageTo();
        $messageTo->setText($command->getTextResponse());
        $messageTo->setChatId($from->getChat()->getId());

        $this->api->sendMessage($messageTo);
    }
}