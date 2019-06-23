<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram\Command;

use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Service\Telegram\Action\ActionContainer;
use Ig0rbm\Memo\Service\Telegram\Command\CommandActionParser;
use Ig0rbm\Memo\Service\Telegram\Command\CommandParser;
use Ig0rbm\Memo\Exception\Telegram\Command\ParseCommandException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @package Ig0rbm\Memo\Tests\Service\Telegram\Command
 */
class CommandActionParserUnitTest extends TestCase
{
    /** @var CommandActionParser */
    private $service;

    /** @var CommandParser|MockObject */
    private $commandParser;

    /** @var ActionContainer|MockObject */
    private $actionContainer;

    public function setUp(): void
    {
        parent::setUp();
        $this->commandParser = $this->createMock(CommandParser::class);
        $this->actionContainer = $this->createMock(ActionContainer::class);

        $this->service = new CommandActionParser($this->commandParser, $this->actionContainer);
    }

     public function testCreateActionListThrowParseCommandException(): void
     {
         $command = new Command();
         $command->setCommand('/command');
         $command->setActionClass('UnknownClass');
         $command->setTextResponse('text response');

         $collection = new HandyBag();
         $collection->set('commandName', $command);

         $this->commandParser->expects($this->once())
             ->method('createCommandCollection')
             ->willReturn($collection);

         $this->expectException(ParseCommandException::class);

         $this->service->createActionList();
     }

    public function testCreateActionListReturnHandyBag(): void
    {
        $this->assertInstanceOf(HandyBag::class, $this->service->createActionList());
    }
}
