<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception;

interface PublicMessageExceptionInterface
{
    public function getTranslationKey(): string;
}
