<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Service\Telegram\Action\ActionInterface;

class HelloAction implements ActionInterface
{
    public function run(string $text): void
    {
        echo 'Hello Action';
    }
}