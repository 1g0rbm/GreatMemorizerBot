<?php

namespace Ig0rbm\Memo\Tests\Service\Telegraph;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ig0rbm\Memo\Service\Telegraph\Request\GetAccount;
use Ig0rbm\Memo\Exception\Telegraph\TelegraphApiException;
use Ig0rbm\Memo\Service\Telegraph\ApiService;
use Ig0rbm\Memo\Entity\Telegraph\Account;
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
        $request = new GetAccount();
        $request->setFields([GetAccount::FIELD_SHORT_NAME, GetAccount::FIELD_AUTHOR_NAME]);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                ApiService::ACCOUNT_INFO,
                [
                    'form_params' => [
                        'access_token'   => self::TOKEN,
                        'fields'         => '["short_name","author_name"]',
                        'return_content' => false
                    ],
                ]
            )
            ->willThrowException(new Exception('test_error_message'));

        $this->expectException(TelegraphApiException::class);

        $this->service->getAccountInfo($request);
    }

    public function testGetAccountInfoThrowExceptionThenBadResponse(): void
    {
        $request = new GetAccount();
        $request->setFields([GetAccount::FIELD_SHORT_NAME, GetAccount::FIELD_AUTHOR_NAME]);

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
                'POST',
                ApiService::ACCOUNT_INFO,
                [
                    'form_params' => [
                        'access_token'   => self::TOKEN,
                        'fields'         => '["short_name","author_name"]',
                        'return_content' => false
                    ],
                ]
            )
            ->willReturn($response);

        $this->expectException(TelegraphApiException::class);

        $this->service->getAccountInfo($request);
    }

    public function testGetAccountInfo(): void
    {
        $request = new GetAccount();
        $request->setFields([GetAccount::FIELD_SHORT_NAME, GetAccount::FIELD_AUTHOR_NAME]);

        $content = [
            'ok' => true,
            'result' => [GetAccount::FIELD_SHORT_NAME => 'short', GetAccount::FIELD_AUTHOR_NAME => 'author']
        ];

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
                'POST',
                ApiService::ACCOUNT_INFO,
                [
                    'form_params' => [
                        'access_token'   => self::TOKEN,
                        'fields'         => '["short_name","author_name"]',
                        'return_content' => false
                    ],
                ]
            )
            ->willReturn($response);

        $response = $this->service->getAccountInfo($request);

        $this->assertInstanceOf(Account::class, $response);
        $this->assertEquals($content['result'][GetAccount::FIELD_AUTHOR_NAME], $response->getAuthorName());
        $this->assertEquals($content['result'][GetAccount::FIELD_SHORT_NAME], $response->getShortName());
    }
}
