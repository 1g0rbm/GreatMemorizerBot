<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Telegram;

use Exception;
use GuzzleHttp\Client;
use Ig0rbm\Memo\Entity\Telegram\Keyboard\ReplyKeyboardRemove;
use Ig0rbm\Memo\Entity\Telegram\Message\AnswerCallbackQuery;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineKeyboard;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\Telegram\Message\ReplyKeyboard;
use Ig0rbm\Memo\Exception\Telegram\SendMessageException;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Serializer as InlineSerializer;
use Ig0rbm\Memo\Service\Telegram\ReplyKeyboard\Serializer as ReplySerializer;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

use function array_filter;
use function json_encode;
use function sprintf;

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

    public function answerCallbackQuery(AnswerCallbackQuery $answerCallbackQuery): string
    {
        $fields = array_filter(
            [
                'callback_query_id' => $answerCallbackQuery->getCallbackQueryId(),
                'text' => $answerCallbackQuery->getText(),
                'show_alert' => $answerCallbackQuery->isShowAlert(),
            ],
            fn($item) => $item !== null
        );

        return $this->send(Request::METHOD_POST, 'answerCallbackQuery', $fields);
    }

    /**
     * @throws Exception
     */
    public function sendMessage(MessageTo $message): string
    {
        $replyKeyboard  = $message->getReplyKeyboard();
        $inlineKeyboard = $message->getInlineKeyboard();
        $removeKeyboard = $message->getReplyKeyboardRemove();

        $replyMarkup = [
            InlineKeyboard::KEY_NAME => $this->inlineSerializer->serialize($inlineKeyboard),
            ReplyKeyboard::KEY_NAME => $this->replySerializer->serialize($replyKeyboard),
            ReplyKeyboardRemove::KEY_NAME => $removeKeyboard ? $removeKeyboard->isRemoveKeyboard() : false,
            'resize_keyboard' => true,
        ];

        $fields = [
            'chat_id' => $message->getChatId(),
            'text' => $message->getText(),
            'parse_mode' => 'markdown',
            'reply_markup' => json_encode($replyMarkup),
        ];

        return $this->send(Request::METHOD_POST, 'sendMessage', $fields);
    }

    private function send(string $httpMethod, string $telegramMethod, array $fields): string
    {
        try {
            $response = $this->client->request(
                $httpMethod,
                sprintf('/bot%s/%s', $this->token, $telegramMethod),
                ['form_params' => $fields]
            );
        } catch (Throwable $e) {
            throw SendMessageException::becauseTransportError($e->getMessage());
        }

        return $response->getBody()->getContents();
    }
}
