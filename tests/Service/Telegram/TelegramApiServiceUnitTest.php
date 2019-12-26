<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Exception;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\Telegram\SendMessageException;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Serializer as InlineSerializer;
use Ig0rbm\Memo\Service\Telegram\ReplyKeyboard\Serializer as ReplySerializer;
use Ig0rbm\Memo\Service\Telegram\TelegramApiService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\Request;

class TelegramApiServiceUnitTest extends TestCase
{
    private const TEST_TOKEN = 'test_token_string';

    private TelegramApiService $service;

    /** @var Client|MockObject */
    private Client $client;

    /** @var InlineSerializer|MockObject */
    private InlineSerializer $inlineSerializer;

    /** @var ReplySerializer|MockObject */
    private ReplySerializer $replySerializer;

    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker            = Factory::create();
        $this->client           = $this->createMock(Client::class);
        $this->inlineSerializer = $this->createMock(InlineSerializer::class);
        $this->replySerializer  = $this->createMock(ReplySerializer::class);

        $this->service = new TelegramApiService(
            $this->client,
            $this->inlineSerializer,
            $this->replySerializer,
            self::TEST_TOKEN
        );
    }

    public function testSendMessageThrowSendMessageException(): void
    {
        $message = $this->getMessage();
        $exceptionMessage = 'test_message';

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                Request::METHOD_POST,
                '/bot' . self::TEST_TOKEN . '/sendMessage',
                [
                    'form_params' => [
                        'chat_id' => $message->getChatId(),
                        'text' => $message->getText(),
                        'parse_mode' => 'markdown',
                        'reply_markup' => json_encode(
                            [
                                'inline_keyboard' => [],
                                'keyboard'  => [],
                                'remove_keyboard' => false,
                                'resize_keyboard' => true,
                            ]
                        )
                    ]
                ]
            )->will($this->throwException(new Exception($exceptionMessage)));

        $this->expectException(SendMessageException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->service->sendMessage($message);
    }

    public function testSendMessageReturnContent(): void
    {
        $message = $this->getMessage();
        $content = 'response';

        $streamInterface = $this->getMockBuilder(StreamInterface::class)->getMock();
        $streamInterface->expects($this->once())
            ->method('getContents')
            ->willReturn($content);

        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($streamInterface);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                Request::METHOD_POST,
                '/bot' . self::TEST_TOKEN . '/sendMessage',
                [
                    'form_params' => [
                        'chat_id' => $message->getChatId(),
                        'text' => $message->getText(),
                        'parse_mode' => 'markdown',
                        'reply_markup' => json_encode(
                            [
                                'inline_keyboard' => [],
                                'keyboard'  => [],
                                'remove_keyboard' => false,
                                'resize_keyboard' => true,
                            ]
                        )
                    ]
                ]
            )->willReturn($response);

        $this->assertSame($content, $this->service->sendMessage($message));
    }

    private function getMessage(): MessageTo
    {
        $message = new MessageTo();
        $message->setChatId($this->faker->unique()->randomNumber(9));
        $message->setText($this->faker->text(100));

        return $message;
    }
}
