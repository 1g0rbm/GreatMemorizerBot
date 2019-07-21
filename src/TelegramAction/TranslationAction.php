<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\Telegram\Message\ChatRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use Ig0rbm\Memo\Service\Translation\TextTranslationService;
use Ig0rbm\Memo\Service\Translation\TranslationService;

class TranslationAction extends AbstractTelegramAction
{
    /** @var TranslationService */
    private $translationService;

    /** @var MessageBuilder */
    private $messageBuilder;

    /** @var TextTranslationService */
    private $textTranslation;

    /** @var ChatRepository */
    private $chatRepository;

    /** @var EntityFlusher */
    private $entityFlusher;

    public function __construct(
        TranslationService $translationService,
        TextTranslationService $textTranslation,
        MessageBuilder $messageBuilder,
        ChatRepository $chatRepository,
        EntityFlusher $entityFlusher
    ) {
        $this->translationService = $translationService;
        $this->textTranslation = $textTranslation;
        $this->messageBuilder = $messageBuilder;
        $this->chatRepository = $chatRepository;
        $this->entityFlusher = $entityFlusher;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $messageTo = new MessageTo();
        $messageTo->setChatId($messageFrom->getChat()->getId());

        if (null === $this->chatRepository->findChatById($messageFrom->getChat()->getId())) {
            $this->chatRepository->addChat($messageFrom->getChat());
            $this->entityFlusher->flush();
        }

        if (null === $messageFrom->getText()->getText()) {
            $messageTo->setText('Wrong text');
            return $messageTo;
        }

        $message = $this->translationService->translate('en-ru', $messageFrom->getText()->getText());
        $messageTo->setText($message);

        return $messageTo;
    }
}