<?php

namespace Ig0rbm\Memo\Validator\Constraints\Translation;

use Ig0rbm\Memo\Entity\Translation\Direction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DirectionAvailableLanguagesConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof DirectionAvailableLanguagesConstraint) {
            throw new UnexpectedTypeException($constraint, DirectionAvailableLanguagesConstraint::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!in_array($value, Direction::$availableLanguages)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ language }}', $value)
                ->addViolation();
        }
    }
}
