<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Exception;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\Telegram\TranslationMessageBuilder;
use Faker\Factory;
use Faker\Generator;

class TranslationMessageBuilderUnitTest extends TestCase
{
    private TranslationMessageBuilder $service;

    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new TranslationMessageBuilder();
        $this->faker   = Factory::create('en_EN');
    }

    /**
     * @throws Exception
     */
    public function testBuildReturnValidString(): void
    {
        $bag = new ArrayCollection();
        $posList = ['noun', 'verb', 'adjective'];
        foreach ($posList as $pos) {
            $word = $this->getWord($pos);
            $bag->add($word);
        }

        $string = $this->service->buildFromWords($bag);

        /** @var Word $word */
        $word = $bag->filter(fn(Word $word) => $word->getPos() === 'adjective')->first();
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
