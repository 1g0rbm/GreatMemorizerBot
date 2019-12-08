<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use PHPUnit\Framework\TestCase;

class MessageBuilderUnitTest extends TestCase
{
    /** @var MessageBuilder */
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new MessageBuilder();
    }

    public function testAppendLn(): void
    {
        $str = 'test';
        $this->service->appendLn($str);

        $this->assertSame($str . PHP_EOL, $this->service->flush());
    }

    public function testAppendLineBreak(): void
    {
        $str = 'test';
        $this->service->append($str)->addLineBreak();

        $this->assertStringEndsWith(PHP_EOL, $this->service->flush());
    }

    public function testAppendAddStringToLine(): void
    {
        $str = 'test';
        $this->service->append($str);

        $this->assertSame($str, $this->service->flush());
        $this->assertSame('', $this->service->flush());
    }

    public function testAppendAddSeveralStringsToLine(): void
    {
        $str1 = 'test';
        $str2 = 'bbb';

        $this->service->append($str1)->append($str2);

        $this->assertSame($str1 . $str2, $this->service->flush());
    }
}
