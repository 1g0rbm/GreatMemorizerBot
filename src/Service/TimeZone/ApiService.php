<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\TimeZone;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Ig0rbm\Memo\Entity\TimeZone\TimeZone;

use function json_decode;

class ApiService
{
    private const GET_TIME_ZONE = '/get-time-zone';

    private Client $client;

    private string $token;

    public function __construct(Client $client, string $token)
    {
        $this->client = $client;
        $this->token  = $token;
    }

    /**
     * @throws GuzzleException
     */
    public function getTimeZone(float $lat, float $lng): TimeZone
    {
        $response = $this->client->request(
            'GET',
            self::GET_TIME_ZONE,
            [
                'query' => [
                    'key' => $this->token,
                    'format' => 'json',
                    'by' => 'position',
                    'lat' => $lat,
                    'lng' => $lng
                ]
            ]
        );

        return TimeZone::createFromResponse(json_decode($response->getBody()->getContents(), true));
    }
}
