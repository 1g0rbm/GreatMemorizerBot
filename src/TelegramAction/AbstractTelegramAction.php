<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Service\Telegram\Action\ActionInterface;
use Ig0rbm\Memo\Service\Telegram\TelegramApiService;

abstract class AbstractTelegramAction implements ActionInterface
{
    /** @var TelegramApiService */
    protected $api;

    public function __construct(TelegramApiService $api)
    {
        $this->api = $api;
    }
}