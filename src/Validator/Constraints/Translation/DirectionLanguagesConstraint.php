<?php

namespace Ig0rbm\Memo\Validator\Constraints\Translation;

use Ig0rbm\Memo\Entity\Translation\Direction;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DirectionLanguagesConstraint extends Constraint
{
    /** @var string */
    public $message = "Field LangFrom {{ lang_from }} can't match with field LangTo {{ lang_to }}";
}
