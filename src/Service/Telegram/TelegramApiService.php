<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Telegram;

use GuzzleHttp\Client;
use Ig0rbm\Memo\Entity\Telegram\Keyboard\ReplyKeyboardRemove;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineKeyboard;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\Telegram\Message\ReplyKeyboard;
use Ig0rbm\Memo\Exception\Telegram\SendMessageException;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Serializer as InlineSerializer;
use Ig0rbm\Memo\Service\Telegram\ReplyKeyboard\Serializer as ReplySerializer;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class TelegramApiService
{
    private Client $client;

    private InlineSerializer $inlineSerializer;

    private ReplySerializer $replySerializer;

    private string $token;

    public function __construct(
        Client $client,
        InlineSerializer $inlineSerializer,
        ReplySerializer $replySerializer,
        string $token
    ) {
        $this->client           = $client;
        $this->inlineSerializer = $inlineSerializer;
        $this->replySerializer  = $replySerializer;
        $this->token            = $token;
    }

    public function sendMessage(MessageTo $message): string
    {
        try {
            $replyMarkup = [
                InlineKeyboard::KEY_NAME => $this->inlineSerializer->serialize($message->getInlineKeyboard()),
                ReplyKeyboard::KEY_NAME => $this->replySerializer->serialize($message->getReplyKeyboard()),
                ReplyKeyboardRemove::KEY_NAME => $message->getReplyKeyboardRemove() ?
                    $message->getReplyKeyboardRemove()->isRemoveKeyboard() : false,
                'resize_keyboard' => true,
            ];

            $response = $this->client->request(
                Request::METHOD_POST,
                '/bot' . $this->token . '/sendMessage',
                [
                    'form_params' => [
                        'chat_id' => $message->getChatId(),
                        'text' => $message->getText(),
                        'parse_mode' => 'markdown',
                        'reply_markup' => json_encode($replyMarkup),
                    ]
                ]
            );
        } catch (Throwable $e) {
            throw SendMessageException::becauseTransportError($e->getMessage());
        }

        return $response->getBody()->getContents();
    }
}
