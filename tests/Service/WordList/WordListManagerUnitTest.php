<?php

namespace Ig0rbm\Memo\Tests\Service\WordList;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\WordList\WordListPreparer;
use Ig0rbm\Memo\Collection\Translation\WordsBag;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Service\WordList\WordListManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WordListManagerUnitTest extends TestCase
{
    private WordListManager $service;

    /** @var EntityManagerInterface|MockObject */
    private $em;

    /** @var WordListPreparer|MockObject */
    private $wordListPreparer;

    /** @var EventDispatcherInterface|MockObject */
    private $eventDispatcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->wordListPreparer = $this->createMock(WordListPreparer::class) ;
        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $this->service = new WordListManager($this->em, $this->wordListPreparer, $this->eventDispatcher);
    }

    public function testAddFlushThenListExistInDB(): void
    {
        $bag = new ArrayCollection();
        $bag->add($this->createWord('noun'));
        $bag->add($this->createWord('verb'));
        $chat = new Chat();
        $wordList = new WordList();

        $unityOfWork = $this->createMock(UnitOfWork::class);
        $unityOfWork->expects($this->once())
            ->method('isInIdentityMap')
            ->with($wordList)
            ->willReturn(true);

        $this->wordListPreparer->expects($this->once())
            ->method('prepare')
            ->with($chat)
            ->willReturn($wordList);

        $this->em->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($unityOfWork);

        $this->em->expects($this->once())
            ->method('flush');

        $this->service->add($chat, $bag);
    }

    private function createWord(string $pos): Word
    {
        $word = new Word();
        $word->setPos($pos);

        return $word;
    }
}
