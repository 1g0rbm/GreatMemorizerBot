<?php

namespace Ig0rbm\Memo\Tests\Service\Translation;

use Ig0rbm\Memo\Service\Translation\TranslationTextParser;
use PHPUnit\Framework\TestCase;

class TranslationTextParserUnitTest extends TestCase
{
    /** @var TranslationTextParser */
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new TranslationTextParser();
    }

    public function testParseReturnText(): void
    {
        $text = 'test';
        $translation = sprintf("%s: noun [some tr] \n переводы", $text);

        $this->assertSame($text, $this->service->parse($translation));
    }
}
