<?php

namespace Ig0rbm\Memo\Service\Telegram\InlineKeyboard;

use Exception;
use Doctrine\Common\Collections\Collection;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
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
            /** @var InlineButton $button */
            foreach ($buttons as $buttonNumber => $button) {
                $result[$lineNumber][$buttonNumber] = [
                    'text' => $button->getText(),
                    'callback_data' => $button->getCallbackData(),
                ];
            }
        }

        return $result;
    }
}
