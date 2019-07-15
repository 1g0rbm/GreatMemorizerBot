<?php

namespace Ig0rbm\Memo\Service\Translation\Yandex;

use Symfony\Component\HttpFoundation\Request;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Text;
use Ig0rbm\Memo\Exception\Translation\Yandex\TranslationException;
use Ig0rbm\Memo\Service\Translation\ApiTextTranslationInterface;
use Ig0rbm\Memo\Exception\Translation\Yandex\TranslationParseException;
use GuzzleHttp\Client;
use Throwable;

class YandexTranslationApiService implements ApiTextTranslationInterface
{
    public const LOOKUP_URI = '/api/v1.5/tr.json/translate';

    /** @var string */
    private $token;

    /** @var Client */
    private $client;

    /** @var TranslationParser */
    private $translationParser;

    public function __construct(string $token, Client $client, TranslationParser $translationParser)
    {
        $this->token = $token;
        $this->client = $client;
        $this->translationParser = $translationParser;
    }

    /**
     * @param Direction $direction
     * @param string $phrase
     * @return Text
     * @throws TranslationException
     * @throws TranslationParseException
     */
    public function getTranslate(Direction $direction, string $phrase): Text
    {
        try {
            $response = $this->client->request(
                Request::METHOD_GET,
                self::LOOKUP_URI,
                [
                    'query' => [
                        'key' => $this->token,
                        'text' => $phrase,
                        'lang' => $direction->getDirection()
                    ]
                ]
            );
        } catch (Throwable $e) {
            throw TranslationException::becauseBadRequestFromYandexApi($e->getMessage());
        }

        return $this->translationParser->parse($response->getBody()->getContents(), $direction);
    }
}