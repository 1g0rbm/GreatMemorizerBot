<?php

namespace Ig0rbm\Memo\Service\Telegram\InlineKeyboard;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Ig0rbm\Memo\Exception\Telegram\InlineKeyboard\InlineKeyboardBuildingException;
use Ig0rbm\Memo\Validator\Constraints\Telegram\InlineKeyboard\InlineButton as AssertInlineButton;

class LineButtonsArrayValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(array $buttons): void
    {
        $errors = $this->validator->validate($buttons, $this->getConstraint());
        if ($errors->count() > 0) {
            throw InlineKeyboardBuildingException::becauseCollectionContainsNotOnlyInlineButtons((string)$errors);
        }
    }

    private function getConstraint(): Assert\All
    {
        return new Assert\All(['constraints' => new AssertInlineButton()]);
    }
}
