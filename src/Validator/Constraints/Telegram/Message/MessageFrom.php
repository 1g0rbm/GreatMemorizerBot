<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Symfony\Component\Validator\Constraint;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom as EntityMessageFrom;

/**
 * @Annotation
 */
class MessageFrom extends Constraint
{
    public string $message = 'Field must be type of ' . EntityMessageFrom::class . '. Instance of {{ instance }} was passed';
}
