<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram\Action;

use Ig0rbm\Memo\Service\Telegram\Action\ActionInterface;
use Symfony\Component\DependencyInjection\Container;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Service\Telegram\Action\ActionContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ActionContainerUnitTest extends TestCase
{
    /** @var ActionContainer */
    private $service;

    /** @var Container|MockObject */
    private $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $this->service = new ActionContainer($this->container);
    }

    public function testGetReturnActionInterface(): void
    {
        $actionName = 'test_name';
        $action = $this->getMockBuilder(ActionInterface::class)->getMock();
        $this->container->expects($this->once())
            ->method('get')
            ->with($actionName)
            ->willReturn($action);

        $this->assertInstanceOf(ActionInterface::class, $this->service->get($actionName));
    }
}
