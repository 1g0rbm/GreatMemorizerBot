<?php

namespace Ig0rbm\Memo\Service\Translation\Yandex;

use Symfony\Component\HttpFoundation\Request;
use Ig0rbm\Memo\Exception\Translation\Yandex\TranslationException;
use Ig0rbm\Memo\Service\Translation\ApiTranslationInterface;
use GuzzleHttp\Client;
use Throwable;

class YandexDictionaryApiService implements ApiTranslationInterface
{
    private const LOOKUP_URI = '/api/v1/dicservice.json/lookup';

    /** @var string */
    private $token;

    /** @var Client */
    private $client;

    public function __construct(string $token, Client $client)
    {
        $this->token = $token;
        $this->client = $client;
    }

    public function getTranslate(string $translateDirection, string $phrase): string
    {
        try {
            $response = $this->client->request(
                Request::METHOD_GET,
                static::LOOKUP_URI,
                [
                    'query' => [
                        'key' => $this->token,
                        'lang' => $translateDirection,
                        'text' => $phrase
                    ]
                ]
            );
        } catch (Throwable $e) {
            throw TranslationException::becauseBadRequestToYandexDictionary($e->getMessage());
        }

        return $response->getBody()->getContents();
    }
}