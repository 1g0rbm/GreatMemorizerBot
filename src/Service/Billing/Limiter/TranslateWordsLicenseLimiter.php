<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Billing\Limiter;

use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\Billing\AccountPrivilegesChecker;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

use function sprintf;

class TranslateWordsLicenseLimiter
{
    private const LIMIT = 20;

    private AccountPrivilegesChecker $checker;

    private AdapterInterface $cache;

    private AccountRepository $accountRepository;

    public function __construct(
        AccountPrivilegesChecker $checker,
        AdapterInterface $cache,
        AccountRepository $accountRepository
    ) {
        $this->checker           = $checker;
        $this->cache             = $cache;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function isLimitReached(Chat $chat): bool
    {
        $account = $this->accountRepository->getOneByChatId($chat->getId());

        if ($this->checker->isFull($account)) {
            return false;
        }

        $item  = $this->cache->getItem(sprintf('%d_word_translate_limit', $account->getId()));
        $count = $item->isHit() ? $item->get() : 0;

        if ($count >= self::LIMIT) {
            return true;
        }

        $item->set($count + 1);
        $item->expiresAt(new DateTimeImmutable('tomorrow midnight'));

        $this->cache->save($item);

        return false;
    }
}
