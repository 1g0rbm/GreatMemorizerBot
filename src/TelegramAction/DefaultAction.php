<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Service\Telegram\Action\ActionInterface;

class DefaultAction implements ActionInterface
{
    public function run(string $text): void
    {
        // TODO: Implement run() method.
        echo 'DefaultAction';
    }
}