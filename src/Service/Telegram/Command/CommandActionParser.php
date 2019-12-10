<?php

namespace Ig0rbm\Memo\Service\Telegram\Command;

use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Exception\Telegram\Command\ParseCommandException;
use Ig0rbm\Memo\Service\Telegram\Action\ActionContainer;

use function class_exists;

class CommandActionParser
{
    private CommandParser $commandParser;

    private ActionContainer $container;

    public function __construct(CommandParser $commandParser, ActionContainer $container)
    {
        $this->commandParser = $commandParser;
        $this->container     = $container;
    }

    /**
     * @throws ParseCommandException
     */
    public function createActionList(): HandyBag
    {
        $collection        = $this->commandParser->createCommandCollection();
        $actionsCollection = new HandyBag();
        $container         = $this->container;

        $collection->walk(static function ($name, Command $command) use ($actionsCollection, $container) {
            if (false === class_exists($command->getActionClass())) {
                throw ParseCommandException::becauseActionClassNotExist($command->getActionClass());
            }

            $actionsCollection->set($command->getActionClass(), $container->get($command->getActionClass()));
        });

        return $actionsCollection;
    }
}
