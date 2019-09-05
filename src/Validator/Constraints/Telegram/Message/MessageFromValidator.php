<?php

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Validator\Constraints\Telegram\Message\MessageFrom as ConstraintMessageFrom;

class MessageFromValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ConstraintMessageFrom) {
            throw new UnexpectedTypeException($constraint, MessageFrom::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_object($value)) {
            throw new UnexpectedValueException($value, MessageFrom::class);
        }

        if (!$value instanceof MessageFrom) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ instance }}', get_class($value))
                ->addViolation();
        }
    }
}
