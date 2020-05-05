<?php

namespace Ig0rbm\Memo\Validator\Constraints\Translation;

use Ig0rbm\Memo\Entity\Translation\Direction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DirectionConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof DirectionConstraint) {
            throw new UnexpectedTypeException($constraint, DirectionConstraint::class);
        }

        if (!is_object($value)) {
            throw new UnexpectedValueException($value, Direction::class);
        }

        if (!$value instanceof Direction) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ instance }}', get_class($value))
                ->addViolation();
        }
    }
}
