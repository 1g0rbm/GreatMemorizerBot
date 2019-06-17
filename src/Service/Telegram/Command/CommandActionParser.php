<?php

namespace Ig0rbm\Memo\Service\Telegram\Command;

use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Exception\Telegram\Command\ParseCommandException;
use Ig0rbm\Memo\Service\Telegram\Action\ActionContainer;

class CommandActionParser
{
    /** @var CommandParser */
    private $commandParser;

    /** @var ActionContainer */
    private $container;

    public function __construct(CommandParser $commandParser, ActionContainer $container)
    {
        $this->commandParser = $commandParser;
        $this->container = $container;
    }

    public function createActionList(): HandyBag
    {
        $collection = $this->commandParser->createCommandCollection();
        $actionsCollection = new HandyBag();

        $collection->walk(function ($name, Command $command) use ($actionsCollection) {
            if (false === class_exists($command->getActionClass())) {
                throw ParseCommandException::becauseActionClassNotExist($command->getActionClass());
            }

            $actionsCollection->set($command->getActionClass(), $this->container->get($command->getActionClass()));
        });

        return $actionsCollection;
    }
}