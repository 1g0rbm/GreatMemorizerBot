<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Symfony\Component\Validator\Constraint;
use Ig0rbm\Memo\Entity\Telegram\Message\From as MessageFrom;

/**
 * @Annotation
 */
class From extends Constraint
{
    public string $message = 'Field must be type of ' . MessageFrom::class . '. Instance of {{ instance }} was passed';
}