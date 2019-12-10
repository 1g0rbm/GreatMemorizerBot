<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Ig0rbm\Memo\Entity\Telegram\Message\Text as MessageText;

/**
 * @Annotation
 */
class Text
{
    public string $message = 'Field must be type of ' . MessageText::class . '. Instance of {{ instance }} was passed';
}
