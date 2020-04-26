<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Repository\Billing;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Account;
use Ig0rbm\Memo\Entity\Billing\License;

class LicenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, License::class);
    }

    public function findLatestLicenseByAccount(Account $account): ?License
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.account = :account')
            ->orderBy('l.dateEnd', 'ASC')
            ->setParameter('account', $account);

        $licenses = $qb->getQuery()->getResult();
        if (empty($licenses)) {
            return null;
        }

        return $licenses[0];
    }

    /**
     * @throws ORMException
     */
    public function addLicense(License $license): void
    {
        $this->getEntityManager()->persist($license);
    }
}
