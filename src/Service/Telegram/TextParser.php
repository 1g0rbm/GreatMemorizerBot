<?php

namespace Ig0rbm\Memo\Service\Telegram;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\Text;

class TextParser
{
    public function parse(string $rawText): Text
    {
        $text = new Text();

        $commandStr = $this->getCommand($rawText);
        $textStr = $this->getText($rawText);

        if ($commandStr) {
            $text->setCommand(trim($commandStr));
        }

        if ($textStr) {
            $text->setText(trim($textStr));
        }

        return $text;
    }

    private function getText(string $rawText): ?string
    {
        $matches = [];
        preg_match('#^/\w+#', $rawText, $matches);

        if (count($matches) === 0) {
            return $rawText;
        }

        $text = trim(str_replace($matches[0], '', $rawText));
        return $text === '' ? null : $text;
    }

    private function getCommand(string $rawText): ?string
    {
        $matches = [];
        preg_match('#^/\w+#', $rawText, $matches);

        return count($matches) >= 1 ? $matches[0] : null;
    }
}