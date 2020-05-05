<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Validator\Constraints;

use Ig0rbm\Memo\Validator\Constraints\Telegram\InlineKeyboard\InlineButton;
use Symfony\Component\Validator\Constraint;

class LicenseStatuses extends Constraint
{
    public string $message = 'Element must be type of ' . InlineButton::class . '. Instance of {{ instance }} was passed.';
}