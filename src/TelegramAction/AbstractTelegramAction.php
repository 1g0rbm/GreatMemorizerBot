<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Service\Telegram\Action\ActionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractTelegramAction implements ActionInterface
{
    protected TranslatorInterface $translator;

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }
}
