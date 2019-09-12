<?php

namespace Ig0rbm\Memo\Tests\Service\WordList;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\WordList\WordListCleaner;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Exception\WordList\WordListCleanerException;

class WordListCleanerUnitTest extends TestCase
{
    /** @var WordListCleaner */
    private $service;

    /** @var WordListRepository|MockObject */
    private $repository;

    /** @var EntityFlusher|MockObject */
    private $flusher;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(WordListRepository::class);
        $this->flusher = $this->createMock(EntityFlusher::class);

        $this->service = new WordListCleaner($this->repository, $this->flusher);
    }

    public function testCleanThrowExceptionIfThereIsNoWordList(): void
    {
        $chat = $this->createChat();
        $this->repository->expects($this->once())
            ->method('findByChat')
            ->with($chat)
            ->willReturn(null);

        $this->expectException(WordListCleanerException::class);

        $this->service->clean($chat, 'word');
    }

    public function testCleanDeleteWordsFromList(): void
    {
        $chat = $this->createChat();
        $list = $this->createWordList();

        $this->repository->expects($this->once())
            ->method('findByChat')
            ->with($chat)
            ->willReturn($list);

        $this->assertEquals(2, $list->getWords()->count());

        $cleanList = $this->service->clean($chat, 'word');
        /** @var Word $word */
        $word = $cleanList->getWords()->first();

        $this->assertEquals(1, $cleanList->getWords()->count());
        $this->assertEquals('house', $word->getText());
    }

    private function createWordList(): WordList
    {
        $word1 = new Word();
        $word1->setPos('noun');
        $word1->setText('word');

        $word2 = new Word();
        $word2->setPos('verb');
        $word2->setText('house');

        $wordList = new WordList();
        $wordList->getWords()->add($word1);
        $wordList->getWords()->add($word2);

        return $wordList;
    }

    private function createChat(): Chat
    {
        $chat = new Chat();
        $chat->setId(1);

        return $chat;
    }
}
