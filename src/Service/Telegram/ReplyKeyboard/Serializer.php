<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Telegram\ReplyKeyboard;

use Doctrine\Common\Collections\Collection;
use Ig0rbm\Memo\Entity\Telegram\Message\ReplyButton;
use Ig0rbm\Memo\Entity\Telegram\Message\ReplyKeyboard;
use Ig0rbm\Memo\Exception\Telegram\InlineKeyboard\InlineKeyboardSerializeException;

class Serializer
{
    public function serialize(?ReplyKeyboard $replyKeyboard): array
    {
        $result = [];

        if ($replyKeyboard === null || $replyKeyboard->getButtonsLines()->count() === 0) {
            return $result;
        }

        /** @var Collection $line */
        foreach ($replyKeyboard->getButtonsLines() as $number => $line) {
            if ($line->count() === 0) {
                InlineKeyboardSerializeException::becauseThereAreNoButtonInLine($number);
            }

            $result[$number] = $line->map(static function (ReplyButton $replyButton) {
                return [
                    'text' => $replyButton->getText(),
                    'request_location' => $replyButton->isRequestLocation(),
                    'request_contact' => $replyButton->isRequestContact()
                ];
            })
            ->toArray();
        }

        return $result;
    }
}
