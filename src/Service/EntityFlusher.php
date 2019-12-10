<?php

namespace Ig0rbm\Memo\Service;

use Doctrine\ORM\EntityManagerInterface;

class EntityFlusher
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function flush(): void
    {
        $this->em->flush();
    }
}