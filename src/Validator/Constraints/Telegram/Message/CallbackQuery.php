<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Ig0rbm\Memo\Entity\Telegram\Message\CallbackQuery as EntityCallbackQuery;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CallbackQuery extends Constraint
{
    public string $message = 'Field must be type of ' . EntityCallbackQuery::class . '. Instance of {{ instance }} was passed';
}
