<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Validator\Constraints\Telegram\Message;

use Ig0rbm\Memo\Entity\Telegram\Message\Location as EntityLocation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class LocationValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Location) {
            throw new UnexpectedTypeException($constraint, Location::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_object($value)) {
            throw new UnexpectedValueException($value, EntityLocation::class);
        }

        if (!$value instanceof EntityLocation) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ instance }}', get_class($value))
                ->addViolation();
        }
    }
}
