<?php

namespace Ig0rbm\Memo\Service\Telegram\Action;

/**
 * All telegram actions must implements this interface
 *
 * @package Ig0rbm\Memo\Service\Telegram\Action
 */
interface ActionInterface
{
    public function run(string $text): void;
}