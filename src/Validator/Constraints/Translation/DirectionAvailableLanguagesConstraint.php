<?php

namespace Ig0rbm\Memo\Validator\Constraints\Translation;

/**
 * @Annotation
 */
class DirectionAvailableLanguagesConstraint
{
    public string $message = 'Passing language({{ language }}) must be in the set of available languages.';
}
