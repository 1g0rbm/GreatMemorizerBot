<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\Billing\LicenseLimitReachedException;
use Ig0rbm\Memo\Exception\WordList\WordListException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\Billing\Limiter\WordListLicenseLimiter;
use Ig0rbm\Memo\Service\Translation\MessageTextFinder;
use Ig0rbm\Memo\Service\Translation\WordTranslationService;
use Ig0rbm\Memo\Service\WordList\WordListManager;

class SaveAction extends AbstractTelegramAction
{
    private WordTranslationService $wordTranslation;

    private AccountRepository $accountRepository;

    private WordListManager $manager;

    private MessageTextFinder $textFinder;

    private WordListLicenseLimiter $limiter;

    public function __construct(
        WordTranslationService $wordTranslation,
        AccountRepository $accountRepository,
        WordListManager $manager,
        MessageTextFinder $textFinder,
        WordListLicenseLimiter $limiter
    ) {
        $this->wordTranslation   = $wordTranslation;
        $this->accountRepository = $accountRepository;
        $this->manager           = $manager;
        $this->textFinder        = $textFinder;
        $this->limiter = $limiter;
    }

    /**
     * @throws ORMException
     * @throws DBALException
     * @throws NonUniqueResultException
     */
    public function run(MessageFrom $from, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($from->getChat()->getId());

        $account  = $this->accountRepository->getOneByChatId($from->getChat()->getId());
        $wordsBag = $this->wordTranslation->translate($account->getDirection(), $this->textFinder->find($from));

        if ($this->limiter->isLimitReached($account)) {
            throw LicenseLimitReachedException::forTranslation();
        }

        if ($wordsBag->count() === 0) {
            $to->setText($this->translator->translate('messages.save.wrong_word', $to->getChatId()));

            return $to;
        }

        try {
            $this->manager->add($from->getChat(), $wordsBag);
            $to->setText($this->translator->translate('messages.save.success', $to->getChatId()));
        } catch (WordListException $e) {
            $to->setText($this->translator->translate($e->getMessage(), $to->getChatId()));
        }

        return $to;
    }
}
