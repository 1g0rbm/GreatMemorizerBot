<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\InlineKeyboard;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton as InlineButtonValue;

class InlineButtonValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof InlineButton) {
            throw new UnexpectedTypeException($constraint, InlineButton::class);
        }

        if (!is_object($value)) {
            throw new UnexpectedValueException($value, InlineButtonValue::class);
        }

        if (!$value instanceof InlineButtonValue) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ instance }}', get_class($value))
                ->addViolation();
        }
    }
}
