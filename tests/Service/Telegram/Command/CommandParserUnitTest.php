<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram\Command;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Service\Telegram\Command\CommandParser;
use Ig0rbm\Memo\Exception\Telegram\Command\ParseCommandException;
use Ig0rbm\HandyBag\HandyBag;

class CommandParserUnitTest extends TestCase
{
    /**
     * @var CommandParser
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new CommandParser($this->getValidRawCommandsCollection());
    }

    /**
     * @throws ParseCommandException
     */
    public function testCreateCommandCollectionThrowParseCommandExceptionBecauseNoTextResponse(): void
    {
        $this->expectException(ParseCommandException::class);
        $this->expectExceptionMessage(sprintf('There is no param: "%s"', CommandParser::KEY_TEXT_RESPONSE));

        $service = new CommandParser(['/hello' => []]);
        $service->createCommandCollection();
    }

    /**
     * @throws ParseCommandException
     */
    public function testCreateCommandCollectionThrowParseCommandExceptionBecauseInvalidName(): void
    {
        $this->expectException(ParseCommandException::class);
        $this->expectExceptionMessage('Invalid telegram command name: "invalid"');

        $service = new CommandParser(['invalid' => []]);
        $service->createCommandCollection();
    }

    /**
     * @throws ParseCommandException
     */
    public function testCreateCommandCollectionReturnFullCollection(): void
    {
        $bag = $this->service->createCommandCollection();

        $this->assertTrue($bag->has('/hello'));

        /** @var Command $command */
        $command = $bag->get('/hello');
        $this->assertSame('Hello you too!', $command->getTextResponse());
        $this->assertSame('/hello', $command->getCommand());

        $this->assertTrue($bag->has('default'));

        /** @var Command $command */
        $command = $bag->get('default');
        $this->assertTrue($command->isDefault());
    }

    /**
     * @throws ParseCommandException
     */
    public function testCreateCommandCollectionReturnCollection(): void
    {
        $this->assertInstanceOf(HandyBag::class, $this->service->createCommandCollection());
    }

    private function getValidRawCommandsCollection(): array
    {
        return [
            '/hello' => [
                'text_response' => 'Hello you too!'
            ],
            'default' => [
                'text_response' => 'undefined command'
            ]
        ];
    }
}
