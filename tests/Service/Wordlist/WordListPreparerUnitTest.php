<?php

namespace Ig0rbm\Memo\Tests\Service\Wordlist;

use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Repository\Telegram\Message\ChatRepository;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\WordList\WordListPreparer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WordListPreparerUnitTest extends TestCase
{
    /** @var WordListPreparer */
    private $service;

    /** @var WordListRepository|MockObject */
    private $wordListRepository;

    /** @var ChatRepository|MockObject */
    private $chatRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->wordListRepository = $this->createMock(WordListRepository::class);
        $this->chatRepository = $this->createMock(ChatRepository::class);

        $this->service = new WordListPreparer($this->wordListRepository, $this->chatRepository);
    }

    public function testPrepareWithExistWordList(): void
    {
        $chat = new Chat();
        $wordList = new WordList();

        $this->wordListRepository->expects($this->once())
            ->method('findByChat')
            ->with($chat)
            ->willReturn($wordList);

        $this->assertSame($wordList, $this->service->prepare($chat));
    }

    public function testPrepareCreateNewEntity(): void
    {
        $chat = new Chat();

        $this->wordListRepository->expects($this->once())
            ->method('findByChat')
            ->with($chat)
            ->willReturn(null);

        $wordList = $this->service->prepare($chat);

        $this->assertInstanceOf(WordList::class, $wordList);
        $this->assertSame($chat, $wordList->getChat());
    }
}
