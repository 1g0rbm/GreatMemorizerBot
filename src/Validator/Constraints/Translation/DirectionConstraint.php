<?php

namespace Ig0rbm\Memo\Validator\Constraints\Translation;

use Ig0rbm\Memo\Entity\Translation\Direction;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DirectionConstraint extends Constraint
{
    /** @var string */
    public $message = 'Field must be type of ' . Direction::class . '. Instance of {{ instance }} was passed.';
}