<?php

namespace Ig0rbm\Memo\Service\Telegram\InlineKeyboard;

use Ig0rbm\Memo\Exception\Telegram\InlineKeyboard\InlineKeyboardBuildingException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Ig0rbm\Memo\Validator\Constraints\Telegram\InlineKeyboard\InlineButton as AssertInlineButton;

class LineButtonsArrayValidator
{
    /** @var ValidatorInterface */
    private $validator;

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
