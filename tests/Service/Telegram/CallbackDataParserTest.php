<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Service\Telegram\CallbackDataParser;
use PHPUnit\Framework\TestCase;

class CallbackDataParserTest extends TestCase
{
    private CallbackDataParser $service;

    public function setUp(): void
    {
        $this->service = new CallbackDataParser();
    }

    /**
     * @dataProvider commandWithoutParameters
     */
    public function testParseReturnValidCallbackDataObject(string $command, array $expected, ?int $aParam): void
    {
        $bag = $this->service->parse($command);

        $this->assertInstanceOf(HandyBag::class, $bag);
        $this->assertEquals($expected, $bag->getAll());
        $this->assertEquals($aParam, $bag->get('a'));
    }

    public function commandWithoutParameters(): array
    {
        return [
            ['/command', [], null],
            ['/command?', [], null],
            ['/command?a=1&b=2', ['a' => 1, 'b' => 2], 1],
            ['/command?a=', [], null],
            ['/command?a', [], null],
        ];
    }
}
