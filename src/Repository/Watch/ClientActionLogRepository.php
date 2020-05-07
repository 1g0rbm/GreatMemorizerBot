<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Repository\Watch;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Ig0rbm\Memo\Entity\Watch\ClientActionLog;

class ClientActionLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientActionLog::class);
    }
}
