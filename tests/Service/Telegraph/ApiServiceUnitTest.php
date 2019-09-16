<?php

namespace Ig0rbm\Memo\Tests\Service\Telegraph;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Exception\Telegraph\TelegraphApiException;
use Ig0rbm\Memo\Service\Telegraph\ApiService;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @group unit
 * @group telegraph
 */
class ApiServiceUnitTest extends TestCase
{
    private const TOKEN = 'test_token';

    /** @var ApiService */
    private $service;

    /** @var Client|MockObject */
    private $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(Client::class);
        $this->service = new ApiService($this->client, self::TOKEN);
    }

    public function testGetAccountInfoThrowExceptionDuringRequest(): void
    {
        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                ApiService::ACCOUNT_INFO,
                [
                    'query' => [
                        'access_token' => self::TOKEN,
                        'fields' => '["short_name","author_name"]',
                    ],
                ]
            )
            ->willThrowException(new Exception('test_error_message'));

        $this->expectException(TelegraphApiException::class);

        $this->service->getAccountInfo();
    }

    public function testGetAccountInfoThrowExceptionThenBadResponse(): void
    {
        $content = ['ok' => false, 'error' => 'error_message'];

        $streamInterface = $this->getMockBuilder(StreamInterface::class)->getMock();
        $streamInterface->expects($this->once())
            ->method('getContents')
            ->willReturn(json_encode($content));

        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($streamInterface);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                ApiService::ACCOUNT_INFO,
                [
                    'query' => [
                        'access_token' => self::TOKEN,
                        'fields' => '["short_name","author_name"]',
                    ],
                ]
            )
            ->willReturn($response);

        $this->expectException(TelegraphApiException::class);

        $this->service->getAccountInfo();
    }

    public function testGetAccountInfo(): void
    {
        $content = ['ok' => true, 'result' => ['field_name' => 'field_value']];

        $streamInterface = $this->getMockBuilder(StreamInterface::class)->getMock();
        $streamInterface->expects($this->once())
            ->method('getContents')
            ->willReturn(json_encode($content));

        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($streamInterface);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                ApiService::ACCOUNT_INFO,
                [
                    'query' => [
                        'access_token' => self::TOKEN,
                        'fields' => '["short_name","author_name"]',
                    ],
                ]
            )
            ->willReturn($response);

        $response = $this->service->getAccountInfo();

        $this->assertTrue($content['ok']);
        $this->assertEquals($content['result']['field_name'], $response['result']['field_name']);
    }
}
