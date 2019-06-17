<?php

namespace Ig0rbm\Memo\Service\Telegram\Command;

use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Exception\Telegram\Command\ParseCommandException;

class CommandParser
{
    public const KEY_TEXT_RESPONSE = 'text_response';
    public const KEY_ACTION_CLASS = 'action_class';

    /** @var array */
    private $rawCommands;

    public function __construct(array $rawCommands)
    {
        $this->rawCommands = $rawCommands;
    }

    /**
     * @throws ParseCommandException
     */
    public function createCommandCollection(): HandyBag
    {
        $bag = new HandyBag();

        foreach ($this->rawCommands as $commandName => $value) {
            if (false === $this->isValidCommandName($commandName)) {
                throw ParseCommandException::becauseInvalidCommandName($commandName);
            }

            if (!isset($value[self::KEY_TEXT_RESPONSE], $value[self::KEY_ACTION_CLASS])) {
                throw ParseCommandException::becauseNoNecessaryParam(self::KEY_TEXT_RESPONSE);
            }

            $command = new Command();
            $command->setTextResponse($value[self::KEY_TEXT_RESPONSE]);
            $command->setActionClass($value[self::KEY_ACTION_CLASS]);
            $command->setCommand($commandName);

            $bag->set($commandName, $command);
        }

        return $bag;
    }

    private function isValidCommandName(string $commandName): bool
    {
        return (boolean)preg_match('#^(/)|(default)#', $commandName);
    }
}