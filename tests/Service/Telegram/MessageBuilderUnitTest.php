<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use Faker\Factory;
use Faker\Generator;

class MessageBuilderUnitTest extends TestCase
{
    /** @var MessageBuilder */
    private $service;

    /** @var Generator */
    private $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new MessageBuilder();
        $this->faker = Factory::create('en_EN');
    }

    public function testBuildReturnValidString(): void
    {
        $bag = new HandyBag();
        $posList = ['noun', 'verb', 'adjective'];
        foreach ($posList as $pos) {
            $word = $this->getWord($pos);
            $bag->set($word->getPos(), $word);
        }

        $string = $this->service->build($bag);

        $word = $bag->get('adjective');
        $this->assertStringEndsWith(sprintf(
            '*%s: **%s **[%s]*%s    %s%s',
            $word->getText(),
            $word->getPos(),
            $word->getTranscription(),
            "\n",
            $word->getTranslations()->first()->getText(),
            "\n"
        ), $string);
    }

    private function getTranslationCollection(): ArrayCollection
    {
        $word = new Word();
        $word->setText($this->faker->word);
        $word->setTranscription($this->faker->word);
        $word->setPos('aaa');

        $translationCollection = new ArrayCollection();
        $translationCollection->add($word);

        return $translationCollection;
    }

    private function getWord(string $pos): Word
    {
        $word = new Word();
        $word->setText($this->faker->word);
        $word->setTranscription($this->faker->word);
        $word->setTranslations($this->getTranslationCollection());
        $word->setPos($pos);

        return $word;
    }
}
