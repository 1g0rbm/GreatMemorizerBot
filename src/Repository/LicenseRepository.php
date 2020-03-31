<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\License;

class LicenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, License::class);
    }

    /**
     * @throws ORMException
     */
    public function addLicense(License $license): void
    {
        $this->getEntityManager()->persist($license);
    }
}
