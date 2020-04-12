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
use Ig0rbm\Memo\Service\Billing\Limiter\LicenseLimiter;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;
use Ig0rbm\Memo\Service\Translation\TranslationService;
use Psr\Cache\InvalidArgumentException;
use DateTimeImmutable;

class TranslationAction extends AbstractTelegramAction
{
    private TranslationService $translationService;

    private LicenseLimiter $limiter;

    private AccountRepository $accountRepository;

    private Builder $builder;

    public function __construct(
        TranslationService $translationService,
        LicenseLimiter $limiter,
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
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        if (null === $messageFrom->getText()->getText()) {
            $to->setText(
                $this->translator->translate('messages.translation_error', $to->getChatId())
            );

            return $to;
        }

        $account       = $this->accountRepository->findOneByChat($messageFrom->getChat());
        $limitExpireAt = new DateTimeImmutable('tomorrow midnight');

        if ($this->limiter->isLimitReached($account, 'word_translate_limit', $limitExpireAt, 20)) {
            $to->setText(
                $this->translator->translate('messages.license.translation_limit_reached', $to->getChatId())
            );

            return $to;
        }

        $message = $this->translationService->translate($account->getDirection(), $messageFrom->getText()->getText());
        $to->setText($message);

        if (! $this->isTranslatedWord($message)) {
            return $to;
        }

        $this->builder->addLine([
            new InlineButton(
                $this->translator->translate('button.inline.save', $to->getChatId()),
                '/save'
            )
        ]);
        $to->setInlineKeyboard($this->builder->flush());

        return $to;
    }

    private function isTranslatedWord(string $text): bool
    {
        $match = [];
        preg_match('#^\*\D+: \*\*\D+ \*\*\[\D+\]\*#', $text, $match);

        return count($match) > 0;
    }
}
