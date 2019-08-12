<?php

namespace Ig0rbm\Memo\Service\Translation;

use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Collection\Translation\WordsBag;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Ig0rbm\Memo\Service\EntityFlusher;

class WordsPersistService
{
    /** @var WordRepository */
    private $repository;

    /** @var EntityFlusher */
    private $flusher;

    public function __construct(WordRepository $repository, EntityFlusher $flusher)
    {
        $this->repository = $repository;
        $this->flusher = $flusher;
    }

    /**
     * @throws ORMException
     */
    public function save(WordsBag $collection): void
    {
        /** @var Word $word */
        foreach ($collection->getIterator() as $word) {
            if ($this->repository->findOneByText($word->getText())) {
                return;
            }

            $this->repository->addWord($word);
        }

        $this->flusher->flush();
    }
}