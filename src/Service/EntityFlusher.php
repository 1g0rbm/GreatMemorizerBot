<?php

namespace Ig0rbm\Memo\Service;

use Doctrine\ORM\EntityManagerInterface;

class EntityFlusher
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function flush(): void
    {
        $this->em->flush();
    }
}