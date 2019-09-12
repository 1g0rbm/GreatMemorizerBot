<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Symfony\Component\Validator\Constraint;
use Ig0rbm\Memo\Entity\Telegram\Message\CallbackQuery as EntityCallbackQuery;

/**
 * @Annotation
 */
class CallbackQuery extends Constraint
{
    public $message = 'Field must be type of ' . EntityCallbackQuery::class . '. Instance of {{ instance }} was passed';
}
