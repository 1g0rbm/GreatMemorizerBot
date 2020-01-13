<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests\Service\TimeZone;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Ig0rbm\Memo\Service\TimeZone\ApiService;
use PHPUnit\Framework\TestCase;

class ApiServiceTest extends TestCase
{
    private const TOKEN = 'token';

    /**
     * @throws GuzzleException
     */
    public function testGetTimeZoneReturnTimeZone(): void
    {
        $service  = new ApiService($this->createClientBehaviorForGetTimeZone(), self::TOKEN);
        $timeZone = $service->getTimeZone(40.689247, -74.044502);

        $this->assertEquals('OK', $timeZone->getStatus());
        $this->assertEquals('America/New_York', $timeZone->getZoneName());
    }

    private function createClientBehaviorForGetTimeZone(): Client
    {
        $mock = new MockHandler([
            new Response(200, [], '{"status":"OK","message":"","countryCode":"US","countryName":"United States","zoneName":"America\/New_York","abbreviation":"EST","gmtOffset":-18000,"dst":"0","zoneStart":1572760800,"zoneEnd":1583650800,"nextAbbreviation":"EDT","timestamp":1578895332,"formatted":"2020-01-13 06:02:12"}')
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    }
}
