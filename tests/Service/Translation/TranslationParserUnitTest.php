<?php

namespace Ig0rbm\Memo\Tests\Service\Translation;

use Faker\Factory;
use Faker\Generator;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Text;
use Ig0rbm\Memo\Service\Translation\Yandex\TranslationParser;
use PHPUnit\Framework\TestCase;

class TranslationParserUnitTest extends TestCase
{
    /** @var TranslationParser */
    private $service;

    /** @var Generator */
    private $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new TranslationParser();
        $this->faker = Factory::create();
    }

    public function testParseReturnText(): void
    {
        $translation = $this->getTranslation();

        $this->assertInstanceOf(
            Text::class,
            $this->service->parse(json_encode($translation), $this->getDirection())
        );
    }

    public function testParserReturnFilledText(): void
    {
        $translation = $this->getTranslation();
        $text = $this->service->parse(json_encode($translation), $this->getDirection());

        $this->assertSame($translation['text'][0], $text->getText());
        $this->assertStringEndsWith($text->getLangCode(), $translation['lang']);
    }

    private function getDirection(): Direction
    {
        $direction = new Direction();
        $direction->setLangTo('en');
        $direction->setLangFrom('ru');
        $direction->setDirection('ru-en');

        return $direction;
    }

    private function getTranslation(): array
    {
        return [
            'code' => 200,
            'lang' => 'ru-en',
            'text' => [
                $this->faker->text(100)
            ]
        ];
    }
}
