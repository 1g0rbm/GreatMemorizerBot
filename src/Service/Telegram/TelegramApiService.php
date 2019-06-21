<?php

namespace Ig0rbm\Memo\Service\Telegram;

use Ig0rbm\Memo\Exception\Telegram\SendMessageException;
use Symfony\Component\HttpFoundation\Request;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use GuzzleHttp\Client;
use Throwable;

class TelegramApiService
{
    /** @var Client */
    private $client;

    /** @var string */
    private $token;

    public function __construct(string $domain, string $token)
    {
        $this->client = new Client(['base_uri' => $domain]);
        $this->token = $token;
    }

    public function sendMessage(MessageTo $message): string
    {
        try {
            $response = $this->client->request(
                Request::METHOD_POST,
                '/bot' . $this->token . '/sendMessage',
                [
                    'form_params' => [
                        'chat_id' => $message->getChatId(),
                        'text' => $message->getText()
                    ]
                ]
            );
        } catch (Throwable $e) {
            throw SendMessageException::becauseTransportError($e->getMessage());
        }

        return $response->getBody()->getContents();
    }
}