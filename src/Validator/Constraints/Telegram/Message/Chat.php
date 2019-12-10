<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Symfony\Component\Validator\Constraint;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat as MessageChat;

/**
 * @Annotation
 */
class Chat extends Constraint
{
    public string $message = 'Field must be type of ' . MessageChat::class . '. Instance of {{ instance }} was passed';
}