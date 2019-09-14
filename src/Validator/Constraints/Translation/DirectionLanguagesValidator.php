<?php

namespace Ig0rbm\Memo\Validator\Constraints\Translation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Ig0rbm\Memo\Entity\Translation\Direction;

class DirectionLanguagesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof DirectionLanguagesConstraint) {
            throw new UnexpectedTypeException($constraint, DirectionLanguagesConstraint::class);
        }

        if (!is_object($value)) {
            throw new UnexpectedValueException($value, Direction::class);
        }

        if (!$value instanceof Direction) {
            throw new UnexpectedValueException($value, Direction::class);
        }

        if ($value->getLangFrom() === $value->getLangTo()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ lang_from }}', $value->getLangFrom())
                ->setParameter('{{ lang_to }}', $value->getLangTo())
                ->addViolation();
        }
    }
}
