<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Billing;

use RuntimeException;

class LicenseLimitReachedException extends RuntimeException
{
    private string $translationLabel;

    public function __construct(string $translationLabel)
    {
        parent::__construct('', 0, null);

        $this->translationLabel = $translationLabel;
    }

    public static function forQuiz(): self
    {
        return new self('messages.errors.quiz_limit_reached');
    }

    public function getTranslationLabel(): string
    {
        return $this->translationLabel;
    }
}
