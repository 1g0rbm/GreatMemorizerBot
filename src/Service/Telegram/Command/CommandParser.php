<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Telegram\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Exception\Telegram\Command\ParseCommandException;

class CommandParser
{
    public const KEY_TEXT_RESPONSE = 'text_response';
    public const KEY_ACTION_CLASS  = 'action_class';
    public const KEY_ALIASES       = 'aliases';

    /** @var array[] */
    private array $rawCommands;

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

            if (!isset($value[self::KEY_TEXT_RESPONSE])) {
                throw ParseCommandException::becauseNoNecessaryParam(self::KEY_TEXT_RESPONSE);
            }

            if (!isset($value[self::KEY_ACTION_CLASS])) {
                throw ParseCommandException::becauseNoNecessaryParam(self::KEY_ACTION_CLASS);
            }

            $command = new Command();
            $command->setTextResponse($value[self::KEY_TEXT_RESPONSE]);
            $command->setActionClass($value[self::KEY_ACTION_CLASS]);
            $command->setCommand($commandName);

            if (isset($value[self::KEY_ALIASES])) {
                $command->setAliases(new ArrayCollection($value[self::KEY_ALIASES]));
            }

            $bag->set($commandName, $command);
        }

        return $bag;
    }

    private function isValidCommandName(string $commandName): bool
    {
        return (boolean)preg_match('#^(/)|(default)#', $commandName);
    }
}
