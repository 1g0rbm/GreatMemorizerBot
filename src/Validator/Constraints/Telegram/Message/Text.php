<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Ig0rbm\Memo\Entity\Telegram\Message\Text as MessageText;

/**
 * @Annotation
 * @package Ig0rbm\Memo\Validator\Constraints\Telegram\Message
 */
class Text
{
    public $message = 'Field must be type of ' . MessageText::class . '. Instance of {{ instance }} was passed';
}