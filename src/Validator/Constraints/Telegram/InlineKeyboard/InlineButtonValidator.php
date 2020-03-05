<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\InlineKeyboard;

use Ig0rbm\Memo\Entity\Telegram\Message\InlineButtonInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class InlineButtonValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof InlineButton) {
            throw new UnexpectedTypeException($constraint, InlineButton::class);
        }

        if (! is_object($value)) {
            throw new UnexpectedValueException($value, InlineButtonInterface::class);
        }

        if (! $value instanceof InlineButtonInterface) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ instance }}', get_class($value))
                ->addViolation();
        }
    }
}
