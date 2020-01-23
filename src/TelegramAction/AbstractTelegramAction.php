<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Service\Telegram\Action\ActionInterface;
use Ig0rbm\Memo\Service\Telegram\TranslationService;

abstract class AbstractTelegramAction implements ActionInterface
{
    protected TranslationService $translator;

    public function setTranslator(TranslationService $translator): void
    {
        $this->translator = $translator;
    }
}
