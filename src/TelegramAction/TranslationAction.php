<?php

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\ORM\ORMException;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\Translation\TextTranslationService;
use Ig0rbm\Memo\Service\Translation\TranslationService;

class TranslationAction extends AbstractTelegramAction
{
    /** @var TranslationService */
    private $translationService;

    /** @var TextTranslationService */
    private $textTranslation;

    /** @var AccountRepository */
    private $accountRepository;

    public function __construct(
        TranslationService $translationService,
        TextTranslationService $textTranslation,
        AccountRepository $accountRepository
    ) {
        $this->translationService = $translationService;
        $this->textTranslation = $textTranslation;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @throws ORMException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $messageTo = new MessageTo();
        $messageTo->setChatId($messageFrom->getChat()->getId());

        if (null === $messageFrom->getText()->getText()) {
            $messageTo->setText('Wrong text');
            return $messageTo;
        }

        $account = $this->accountRepository->findOneByChat($messageFrom->getChat());
        $message = $this->translationService->translate($account->getDirection(), $messageFrom->getText()->getText());
        $messageTo->setText($message);

        return $messageTo;
    }
}
