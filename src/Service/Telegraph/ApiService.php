<?php

namespace Ig0rbm\Memo\Service\Telegraph;

use Ig0rbm\Memo\Exception\Telegraph\TelegraphApiException;
use Throwable;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

class ApiService
{
    public const ACCOUNT_INFO = '/getAccountInfo';

    /** @var Client */
    private $client;

    /** @var string */
    private $token;

    public function __construct(Client $client, string $token)
    {
        $this->client = $client;
        $this->token = $token;
    }

    public function getAccountInfo(?array $fields = null): array
    {
        $fields = $fields ?? ['short_name', 'author_name'];

        try {
            $response = $this->client->request(
                Request::METHOD_GET,
                self::ACCOUNT_INFO,
                [
                    'query' => [
                        'access_token' => $this->token,
                        'fields' => $this->serializeFields($fields)
                    ]
                ]
            );
        } catch (Throwable $e) {
            throw TelegraphApiException::becauseBadRequestToTelegraph($e->getMessage());
        }

        $content = json_decode($response->getBody()->getContents(), true);
        if ($content['ok'] === false) {
            throw TelegraphApiException::becauseBadResponseFromTelegraph($content['error']);
        }

        return $content;
    }

    private function serializeFields(array $fields): string
    {
        $serialized = '';
        foreach ($fields as $field) {
            $serialized .= $serialized === '' ? sprintf('"%s"', $field) : sprintf(',"%s"', $field);
        }

        return $serialized === '' ?: sprintf('[%s]', $serialized) ;
    }
}
