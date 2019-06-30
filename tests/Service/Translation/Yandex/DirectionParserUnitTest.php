<?php

namespace Ig0rbm\Memo\Tests\Service\Translation\Yandex;

use Ig0rbm\Memo\Entity\Translation\Yandex\Direction;
use Ig0rbm\Memo\Service\Translation\Yandex\DirectionParser;
use PHPUnit\Framework\TestCase;

class DirectionParserUnitTest extends TestCase
{
    /** @var DirectionParser */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new DirectionParser();
    }

    public function testParseReturnDirection(): void
    {
        $langFrom = 'ru';
        $langTo = 'en';
        $rawDirection = sprintf('%s-%s', $langFrom, $langTo);

        $direction = $this->service->parse($rawDirection);

        $this->assertInstanceOf(Direction::class, $direction);
        $this->assertSame($langFrom, $direction->getLangFrom());
        $this->assertSame($langTo, $direction->getLangTo());
        $this->assertSame($rawDirection, $direction->getDirection());
    }
}
