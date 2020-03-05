<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Telegram\InlineKeyboard;

use Doctrine\Common\Collections\Collection;
use Exception;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButtonInterface;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineKeyboard;
use Ig0rbm\Memo\Exception\Telegram\InlineKeyboard\InlineKeyboardSerializeException;

class Serializer
{
    /**
     * @throws Exception
     */
    public function serialize(?InlineKeyboard $keyboard): array
    {
        $result = [];

        if (!$keyboard || $keyboard->getButtonsLines()->count() === 0) {
            return $result;
        }

        /** @var Collection $line */
        foreach ($keyboard->getButtonsLines() as $lineNumber => $line) {
            if ($line->count() === 0) {
                InlineKeyboardSerializeException::becauseThereAreNoButtonInLine($lineNumber);
            }

            $result[$lineNumber] = [];
            $buttons = $line->getIterator();

            /** @var InlineButtonInterface $button */
            foreach ($buttons as $buttonNumber => $button) {
                $result[$lineNumber][$buttonNumber] = [
                    'text'            => $button->getText(),
                    $button->getKey() => $button->getCallbackData(),
                ];
            }
        }

        return $result;
    }
}
