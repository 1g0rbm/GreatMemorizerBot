<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Exception\AccountNotFoundException;
use Ig0rbm\Memo\Exception\Telegram\AccountException;

class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @return Account[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getOneById(int $accountId): Account
    {
        $account = $this->findOneById($accountId);
        if ($account === null) {
            throw AccountNotFoundException::byAccountId($accountId);
        }

        return $account;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneById(int $accountId): ?Account
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.id = :accountId')
            ->setParameter('accountId', $accountId);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getOneByChatId(int $chatId): Account
    {
        /** @var Account $account */
        $account = $this->findOneBy(['chatId' => $chatId]);
        if ($account === null) {
            throw AccountException::becauseThereIsNotAccountForChat($chatId);
        }

        return $account;
    }

    public function findOneByChat(Chat $chat): ?Account
    {
        /** @var Account $account */
        $account = $this->findOneBy(['chat' => $chat]);

        return $account;
    }

    /**
     * @throws ORMException
     */
    public function addAccount(Account $account): void
    {
        $this->getEntityManager()->persist($account);
    }
}
