<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Validator;

use Ig0rbm\Memo\Exception\PublicMessageExceptionInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class EntityValidationException extends ValidatorException implements PublicMessageExceptionInterface
{
    private string $translationKey;

    public function __construct(string $errorMessages)
    {
        parent::__construct($errorMessages, 400, null);

        $this->translationKey = 'messages.errors.invalid_data';
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }
}
