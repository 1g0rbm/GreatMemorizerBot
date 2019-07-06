<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Ig0rbm\Memo\Entity\Translation\Word;
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

    public function testBuildReturnValidString(): void
    {

    }

    private function getWord(): Word
    {
        $word = new Word();
        $word->setText('test');

        return $word;
    }
}
