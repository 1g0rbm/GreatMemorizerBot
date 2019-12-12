<?php

namespace Ig0rbm\Memo\Service\Translation;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Ig0rbm\Memo\Service\EntityFlusher;

class WordsPersistService
{
    private WordRepository $repository;

    private EntityFlusher $flusher;

    public function __construct(WordRepository $repository, EntityFlusher $flusher)
    {
        $this->repository = $repository;
        $this->flusher    = $flusher;
    }

    /**
     * @throws ORMException
     */
    public function save(Collection $collection): void
    {
        /** @var Word $word */
        foreach ($collection->toArray() as $word) {
            if ($this->repository->findOneByText($word->getText())) {
                return;
            }

            $this->repository->addWord($word);
        }

        $this->flusher->flush();
    }
}
