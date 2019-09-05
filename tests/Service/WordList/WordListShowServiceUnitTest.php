<?php

namespace Ig0rbm\Memo\Tests\Service\WordList;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Collection\Translation\WordsBag;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\WordList\WordListShowService;
use Faker\Generator;
use Faker\Factory;

class WordListShowServiceTest extends TestCase
{
    /** @var WordListShowService */
    private $service;

    /** @var WordListRepository|MockObject */
    private $wordListRepository;

    /** @var MessageBuilder */
    private $builder;

    /** @var Generator  */
    private $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->builder = new MessageBuilder();
        $this->wordListRepository = $this->createMock(WordListRepository::class);

        $this->service = new WordListShowService($this->wordListRepository, new MessageBuilder());
    }

    public function testFindByChatReturnNullIfThereAreNotList(): void
    {
        $list = $this->createWordList();

        $this->wordListRepository->expects($this->once())
            ->method('findByChat')
            ->with($list->getChat())
            ->willReturn(null);

        $this->assertNull($this->service->findByChat($list->getChat()));
    }

    public function testFindByChatReturnSerializedString(): void
    {
        $list = $this->createWordList();

        $bag = new WordsBag();
        /** @var Word $word */
        foreach ($list->getWords() as $word) {
            $bag->setWord($word);
        }

        $this->wordListRepository->expects($this->once())
            ->method('findByChat')
            ->with($list->getChat())
            ->willReturn($list);

        $this->assertEquals($this->builder->buildFromWords($bag), $this->service->findByChat($list->getChat()));
    }

    private function createWordList(): WordList
    {
        $wordList = new WordList();
        $wordList->setChat($this->getChat());
        $wordList->setWords($this->getWordsBag());

        return $wordList;
    }

    private function getWordsBag(): ArrayCollection
    {
        $word1 = new Word();
        $word1->setText($this->faker->unique()->word);
        $word1->setPos('noun');
        $word1->setTranscription($this->faker->unique()->word);
        $word1->setTranslations(new ArrayCollection());

        $word2 = new Word();
        $word2->setText($this->faker->unique()->word);
        $word2->setPos('verb');
        $word2->setTranscription($this->faker->unique()->word);
        $word2->setTranslations(new ArrayCollection());

        $collection = new ArrayCollection();
        $collection->add($word1);
        $collection->add($word2);

        return $collection;
    }

    private function getChat(): Chat
    {
        $chat = new Chat();
        $chat->setId($this->faker->unique()->randomNumber(9));

        return $chat;
    }
}
