<?php

namespace Ig0rbm\Memo\Service\Telegram;

use Throwable;
use Symfony\Component\HttpFoundation\Request;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\Telegram\SendMessageException;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Serializer;
use GuzzleHttp\Client;

class TelegramApiService
{
    /** @var Client */
    private $client;

    /** @var Serializer */
    private $serializer;

    /** @var string */
    private $token;

    public function __construct(Client $client, Serializer $serializer, string $token)
    {
        $this->client   = $client;
        $this->serializer = $serializer;
        $this->token    = $token;
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
                        'text' => $message->getText(),
                        'parse_mode' => 'markdown',
                        'reply_markup' => json_encode([
                            'inline_keyboard' => $this->serializer->serialize($message->getInlineKeyboard())
                        ])
                    ]
                ]
            );
        } catch (Throwable $e) {
            throw SendMessageException::becauseTransportError($e->getMessage());
        }

        return $response->getBody()->getContents();
    }
}
