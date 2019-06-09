<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Symfony\Component\Validator\Constraint;
use Ig0rbm\Memo\Entity\Telegram\Message\From as MessageFrom;

/**
 * @Annotation
 * @package Ig0rbm\Memo\Validator\Constraints\Telegram\Message
 */
class From extends Constraint
{
    public $message = 'Field must be type of ' . MessageFrom::class . '. Instance of {{ instance }} was passed';
}