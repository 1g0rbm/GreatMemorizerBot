<?php

namespace Ig0rbm\Memo\Service\Translation;

use function stristr;

class TranslationTextParser
{
    private const TEXT_DIVIDER = ':';

    public function parse(string $pattern): string
    {
       return stristr($pattern, self::TEXT_DIVIDER, true);
    }
}
