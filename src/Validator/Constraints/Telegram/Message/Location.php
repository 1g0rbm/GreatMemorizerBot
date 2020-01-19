<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Ig0rbm\Memo\Entity\Telegram\Message\Location as EntityLocation;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Location extends Constraint
{
    public string $message = 'Field must be type of ' . EntityLocation::class . '. Instance of {{ instance }} was passed';
}
