<?php

namespace Ig0rbm\Memo\Tests\Service\Translation;

use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Collection\Translation\WordsBag;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Ig0rbm\Memo\Service\Translation\WordsPersistService;

class WordsPersistServiceUnitTest extends TestCase
{
    /** @var WordsPersistService */
    private $service;

    /** @var WordRepository|MockObject */
    private $wordRepository;

    /** @var EntityFlusher|MockObject */
    private $flusher;

    public function setUp(): void
    {
        parent::setUp();

        $this->wordRepository = $this->createMock(WordRepository::class);
        $this->flusher = $this->createMock(EntityFlusher::class);

        $this->service = new WordsPersistService($this->wordRepository, $this->flusher);
    }

    /**
     * @throws ORMException
     */
    public function testSaveStoppedWhenThereAreNoWords(): void
    {
        $bag = new WordsBag();

        $this->wordRepository->expects($this->never())->method('findOneByText');
        $this->wordRepository->expects($this->never())->method('addWord');
        $this->flusher->expects($this->once())->method('flush');

        $this->service->save($bag);
    }

    /**
     * @throws ORMException
     */
    public function testSaveWithFullBag(): void
    {
        $bag = $this->getWordsBagWithTwoWords();

        $this->wordRepository->expects($this->at(0))
            ->method('findOneByText')
            ->with($bag->getWordByPos(Word::POS_NOUN)->getText());

        $this->wordRepository->expects($this->at(1))
            ->method('addWord')
            ->with($bag->getWordByPos(Word::POS_NOUN));

        $this->wordRepository->expects($this->at(2))
            ->method('findOneByText')
            ->with($bag->getWordByPos(Word::POS_VERB)->getText());

        $this->wordRepository->expects($this->at(3))
            ->method('addWord')
            ->with($bag->getWordByPos(Word::POS_VERB));

        $this->flusher->expects($this->once())->method('flush');

        $this->service->save($bag);
    }

    private function getWordsBagWithTwoWords(): WordsBag
    {
        $bag = new WordsBag();

        $word1 = new Word();
        $word1->setText('word1');
        $word1->setPos(Word::POS_NOUN);

        $word2 = new Word();
        $word2->setText('word2');
        $word2->setPos(Word::POS_VERB);

        $bag->setWord($word1);
        $bag->setWord($word2);

        return $bag;
    }
}
