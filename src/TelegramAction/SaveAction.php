<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Translation\TranslationService;

class SaveAction extends AbstractTelegramAction
{
    /** @var TranslationService */
    private $translation;

    public function __construct(TranslationService $translation)
    {
        $this->translation = $translation;
    }

    public function run(MessageFrom $from, Command $command): MessageTo
    {
        $messageTo = new MessageTo();
        $messageTo->setChatId($from->getChat()->getId());

        if (null === $from->getText()->getText()) {
            $from->setText('Wrong text');
            return $messageTo;
        }



        $messageTo->setText($command->getTextResponse());

        return $messageTo;
    }
}