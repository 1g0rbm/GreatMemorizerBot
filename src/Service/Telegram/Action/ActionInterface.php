<?php

namespace Ig0rbm\Memo\Service\Telegram\Action;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;

/**
 * All telegram actions must implements this interface
 *
 * @package Ig0rbm\Memo\Service\Telegram\Action
 */
interface ActionInterface
{
    public function run(MessageFrom $messageFrom, Command $command): MessageTo;
}
