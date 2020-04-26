<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Repository\Billing\Patreon;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Billing\Patreon\Pledge;

class PledgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pledge::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByEmail(string $email): ?Pledge
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.email = :email')
            ->setParameter('email', $email);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @throws ORMException
     */
    public function addPledge(Pledge $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}
