<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Ig0rbm\Memo\Entity\Telegram\Message\Text;
use Ig0rbm\Memo\Service\Telegram\CallbackDataParser;
use Ig0rbm\Memo\Service\Telegram\TextParser;
use PHPUnit\Framework\TestCase;

class TextParserTest extends TestCase
{
    private TextParser $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new TextParser(new CallbackDataParser());
    }

    public function testParseReturnText(): void
    {
        $textString = 'string';
        $text = $this->service->parse($textString);

        $this->assertInstanceOf(Text::class, $text);
    }

    public function testParseReturnTextWithText(): void
    {
        $textString = 'delete:word1';
        $text = $this->service->parse($textString);

        $this->assertSame($textString, $text->getText());
        $this->assertNull($text->getCommand());
    }

    public function testParseReturnTextWithCommand(): void
    {
        $textString = '/command';
        $text = $this->service->parse($textString);

        $this->assertSame($textString, $text->getCommand());
        $this->assertNull($text->getText());
    }

    public function testParseReturnTextWithCommandAndText(): void
    {
        $commandString = '/command_name';
        $textString = 'string string';
        $text = $this->service->parse("$commandString $textString");

        $this->assertSame($commandString, $text->getCommand());
        $this->assertSame($textString, $text->getText());
    }
}
