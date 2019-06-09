<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class FromValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof From) {
            throw new UnexpectedTypeException($constraint, From::class);
        }

        if (!is_object($value)) {
            throw new UnexpectedValueException($value, \Ig0rbm\Memo\Entity\Telegram\Message\From::class);
        }

        if (!$value instanceof \Ig0rbm\Memo\Entity\Telegram\Message\From) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ instance }}', get_class($value))
                ->addViolation();
        }
    }
}