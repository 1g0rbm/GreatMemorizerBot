<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Telegram;

use Ig0rbm\HandyBag\HandyBag;

use function array_pop;
use function array_reduce;
use function count;
use function explode;
use function strstr;

class CallbackDataParser
{
    public function parse(string $rawString): HandyBag
    {
        return new HandyBag($this->getParamsArray($rawString));
    }

    private function getParamsArray(string $rawString): array
    {
        $params = $this->getParamsString($rawString);
        if (empty($params)) {
            return [];
        }

        return array_reduce(
            explode('&', $params),
            static function (array $acc, string $pair) {
                if (strstr($pair, '=') === false) {
                    return $acc;
                }

                [$key, $value] = explode('=', $pair);
                if (!$key || !$value) {
                    return $acc;
                }

                $acc[$key] = $value;

                return $acc;
            },
            []
        );
    }

    private function getParamsString(string $rawString): ?string
    {
        $explodedString = explode('?', $rawString);
        if (count($explodedString) <= 1) {
            return null;
        }

        return array_pop($explodedString);
    }
}
