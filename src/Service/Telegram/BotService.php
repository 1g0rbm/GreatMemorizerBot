<?php

namespace Ig0rbm\Memo\Service\Telegram;

class BotService
{
    /**
     * @var MessageParser
     */
    private $parser;

    public function __construct(MessageParser $parser)
    {
        $this->parser = $parser;
    }

    public function handle(string $raw): void
    {
        $message = $this->parser->createMessage($raw);
    }
}