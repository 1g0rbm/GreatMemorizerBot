<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use Ig0rbm\Memo\Service\Translation\DirectionParser;
use Ig0rbm\Memo\Service\Translation\TranslationService;

class TranslationAction extends AbstractTelegramAction
{
    /** @var TranslationService */
    private $translationService;

    /** @var MessageBuilder */
    private $messageBuilder;

    public function __construct(
        TranslationService $translationService,
        MessageBuilder $messageBuilder
    ) {
        $this->translationService = $translationService;
        $this->messageBuilder = $messageBuilder;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $messageTo = new MessageTo();
        $messageTo->setChatId($messageFrom->getChat()->getId());

        if (null === $messageFrom->getText()->getText()) {
            $messageTo->setText('Wrong text');
            return $messageTo;
        }

        $words = $this->translationService->translate('en-ru', $messageFrom->getText()->getText());
        $text = $this->messageBuilder->build($words) ?: 'No translation or invalid word';

        $messageTo->setText($text);

        return $messageTo;
    }
}