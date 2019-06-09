<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Symfony\Component\Validator\Constraint;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat as MessageChat;

/**
 * @Annotation
 * @package Ig0rbm\Memo\Validator\Constraints\Telegram\Message
 */
class Chat extends Constraint
{
    public $message = 'Field must be type of ' . MessageChat::class . '. Instance of {{ instance }} was passed';
}