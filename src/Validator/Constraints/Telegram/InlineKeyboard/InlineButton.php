<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\InlineKeyboard;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotaion
 */
class InlineButton extends Constraint
{
    public string $message = 'Element must be type of ' . InlineButton::class . '. Instance of {{ instance }} was passed.';
}
