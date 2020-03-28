<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Telegram\Command;

use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Exception\Telegram\Command\ParseCommandException;
use Ig0rbm\Memo\Service\Telegram\Command\CommandParser;
use PHPUnit\Framework\TestCase;

class CommandParserUnitTest extends TestCase
{

    private CommandParser $service;

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
        $this->assertEquals(0, $command->getAliases()->count());

        $this->assertTrue($bag->has('default'));

        /** @var Command $command */
        $command = $bag->get('default');
        $this->assertTrue($command->isDefault());
    }

    /**
     * @throws ParseCommandException
     */
    public function testCreateCommandWithAlias(): void
    {
        $bag = $this->service->createCommandCollection();

        $this->assertTrue($bag->has('/test'));

        /** @var Command $command */
        $command = $bag->get('/test');
        $this->assertEquals(2, $command->getAliases()->count());
        $this->assertEquals('ru-en', $command->getAliases()->first());
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
                'text_response' => 'Hello you too!',
                'action_class'  => 'Test/ClassName',
            ],
            '/test' => [
                'text_response' => 'Test!',
                'action_class'  => 'Test/ClassName',
                'aliases'       => ['ru-en', 'en-ru'],
            ],
            'default' => [
                'text_response' => 'undefined command',
                'action_class'  => 'Test/ClassName'
            ]
        ];
    }
}
