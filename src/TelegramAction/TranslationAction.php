<?php

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;
use Ig0rbm\Memo\Service\Translation\TranslationService;

class TranslationAction extends AbstractTelegramAction
{
    /** @var TranslationService */
    private $translationService;

    /** @var AccountRepository */
    private $accountRepository;

    /** @var Builder */
    private $builder;

    public function __construct(
        TranslationService $translationService,
        AccountRepository $accountRepository,
        Builder $builder
    ) {
        $this->translationService = $translationService;
        $this->accountRepository  = $accountRepository;
        $this->builder            = $builder;
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

        if (! $this->isTranslatedWord($message)) {
            return $messageTo;
        }

        $this->builder->addLine([new InlineButton('save', '/save')]);
        $messageTo->setInlineKeyboard($this->builder->flush());

        return $messageTo;
    }

    private function isTranslatedWord(string $text): bool
    {
        $match = [];
        preg_match('#^\*\D+: \*\*\D+ \*\*\[\D+\]\*#', $text, $match);

        return count($match) > 0;
    }
}
