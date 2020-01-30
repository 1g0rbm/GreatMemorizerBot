<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\WordList\WordListException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\Translation\MessageTextFinder;
use Ig0rbm\Memo\Service\Translation\WordTranslationService;
use Ig0rbm\Memo\Service\WordList\WordListManager;

class SaveAction extends AbstractTelegramAction
{
    private WordTranslationService $wordTranslation;

    private AccountRepository $accountRepository;

    private WordListManager $manager;

    private MessageTextFinder $textFinder;

    public function __construct(
        WordTranslationService $wordTranslation,
        AccountRepository $accountRepository,
        WordListManager $manager,
        MessageTextFinder $textFinder
    ) {
        $this->wordTranslation   = $wordTranslation;
        $this->accountRepository = $accountRepository;
        $this->manager           = $manager;
        $this->textFinder        = $textFinder;
    }

    /**
     * @throws ORMException
     */
    public function run(MessageFrom $from, Command $command): MessageTo
    {
        $messageTo = new MessageTo();
        $messageTo->setChatId($from->getChat()->getId());

        $account  = $this->accountRepository->getOneByChatId($from->getChat()->getId());
        $wordsBag = $this->wordTranslation->translate($account->getDirection(), $this->textFinder->find($from));

        if ($wordsBag->count() === 0) {
            $messageTo->setText($this->translator->translate('messages.save.wrong_word', $messageTo->getChatId()));

            return $messageTo;
        }

        try {
            $this->manager->add($from->getChat(), $wordsBag);
            $messageTo->setText($this->translator->translate('messages.save.success', $messageTo->getChatId()));
        } catch (WordListException $e) {
            $messageTo->setText($this->translator->translate($e->getMessage(), $messageTo->getChatId()));
        }

        return $messageTo;
    }
}
