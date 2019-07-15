<?php

namespace Ig0rbm\Memo\Service\Translation\Yandex;

use Symfony\Component\HttpFoundation\Request;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Exception\Translation\Yandex\TranslationException;
use Ig0rbm\Memo\Service\Translation\ApiWordTranslationInterface;
use Ig0rbm\HandyBag\HandyBag;
use GuzzleHttp\Client;
use Throwable;

class YandexDictionaryApiService implements ApiWordTranslationInterface
{
    private const LOOKUP_URI = '/api/v1/dicservice.json/lookup';

    /** @var string */
    private $token;

    /** @var Client */
    private $client;

    /** @var DictionaryParser */
    private $parser;

    public function __construct(string $token, Client $client, DictionaryParser $parser)
    {
        $this->token = $token;
        $this->client = $client;
        $this->parser = $parser;
    }

    public function getTranslate(Direction $direction, string $phrase): HandyBag
    {
        try {
            $response = $this->client->request(
                Request::METHOD_GET,
                static::LOOKUP_URI,
                [
                    'query' => [
                        'key' => $this->token,
                        'lang' => $direction->getDirection(),
                        'text' => $phrase
                    ]
                ]
            );
        } catch (Throwable $e) {
            throw TranslationException::becauseBadRequestFromYandexApi($e->getMessage());
        }

        return $this->parser->parse($response->getBody()->getContents(), $direction);
    }
}