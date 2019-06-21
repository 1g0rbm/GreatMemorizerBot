<?php

namespace Ig0rbm\Memo\Tests\Service\Telegram;

use Ig0rbm\Memo\Exception\Telegram\SendMessageException;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Service\Telegram\TelegramApiService;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Exception;

class TelegramApiServiceUnitTest extends TestCase
{
    private const TEST_DOMAIN = 'http://api.telegram.org';
    private const TEST_TOKEN = 'test_token_string';

    /** @var TelegramApiService|MockObject */
    private $service;

    /** @var Client|MockObject */
    private $client;

    /** @var Generator */
    private $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->client = $this->createMock(Client::class);

        $this->service = new TelegramApiService($this->client, self::TEST_TOKEN);
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
                        'text' => $message->getText()
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
                        'text' => $message->getText()
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
