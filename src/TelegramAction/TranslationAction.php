<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\Billing\Limiter\TranslateWordsLicenseLimiter;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;
use Ig0rbm\Memo\Service\Translation\TranslationService;
use Psr\Cache\InvalidArgumentException;

class TranslationAction extends AbstractTelegramAction
{
    private TranslationService $translationService;

    private TranslateWordsLicenseLimiter $limiter;

    private AccountRepository $accountRepository;

    private Builder $builder;

    public function __construct(
        TranslationService $translationService,
        TranslateWordsLicenseLimiter $limiter,
        AccountRepository $accountRepository,
        Builder $builder
    ) {
        $this->translationService = $translationService;
        $this->limiter            = $limiter;
        $this->accountRepository  = $accountRepository;
        $this->builder            = $builder;
    }

    /**
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $messageTo = new MessageTo();
        $messageTo->setChatId($messageFrom->getChat()->getId());

        if (null === $messageFrom->getText()->getText()) {
            $messageTo->setText(
                $this->translator->translate('messages.translation_error', $messageTo->getChatId())
            );

            return $messageTo;
        }

        $account = $this->accountRepository->findOneByChat($messageFrom->getChat());

        if ($this->limiter->isLimitReached($messageFrom->getChat())) {
            $messageTo->setText(
                $this->translator->translate('messages.license.translation_limit_reached', $messageTo->getChatId())
            );

            return $messageTo;
        }

        $message = $this->translationService->translate($account->getDirection(), $messageFrom->getText()->getText());
        $messageTo->setText($message);

        if (! $this->isTranslatedWord($message)) {
            return $messageTo;
        }

        $this->builder->addLine([
            new InlineButton(
                $this->translator->translate('button.inline.save', $messageTo->getChatId()),
                '/save'
            )
        ]);
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
