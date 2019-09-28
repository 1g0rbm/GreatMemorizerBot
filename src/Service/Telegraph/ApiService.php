<?php

namespace Ig0rbm\Memo\Service\Telegraph;

use Ig0rbm\Memo\Entity\Telegraph\Account;
use Ig0rbm\Memo\Service\Telegraph\Request\BaseRequest;
use Ig0rbm\Memo\Service\Telegraph\Request\CreatePage;
use Ig0rbm\Memo\Service\Telegraph\Request\GetAccount;
use Throwable;
use Symfony\Component\HttpFoundation\Request;
use Ig0rbm\Memo\Entity\Telegraph\Page;
use Ig0rbm\Memo\Exception\Telegraph\TelegraphApiException;
use GuzzleHttp\Client;

class ApiService
{
    public const ACCOUNT_INFO = '/getAccountInfo';
    public const CREATE_PAGE = '/createPage';

    /** @var Client */
    private $client;

    /** @var string */
    private $token;

    public function __construct(Client $client, string $token)
    {
        $this->client = $client;
        $this->token = $token;
    }

    public function createPage(CreatePage $request): Page
    {
        try {
            $response = $this->client->request(
                Request::METHOD_GET,
                self::CREATE_PAGE,
                $this->prepareQuery($request)
            );
        } catch (Throwable $e) {
            throw TelegraphApiException::becauseBadRequestToTelegraph($e->getMessage());
        }

        $content = json_decode($response->getBody()->getContents(), true);
        if ($content['ok'] === false) {
            throw TelegraphApiException::becauseBadResponseFromTelegraph($content['error']);
        }

        return Page::createFromTelegraphResponse($content['result']);
    }

    public function getAccountInfo(GetAccount $request): Account
    {
        try {
            $response = $this->client->request(
                Request::METHOD_GET,
                self::ACCOUNT_INFO,
                $this->prepareQuery($request)
            );
        } catch (Throwable $e) {
            throw TelegraphApiException::becauseBadRequestToTelegraph($e->getMessage());
        }

        $content = json_decode($response->getBody()->getContents(), true);
        if ($content['ok'] === false) {
            throw TelegraphApiException::becauseBadResponseFromTelegraph($content['error']);
        }

        return Account::createFromTelegraphResponse($content['result']);
    }

    private function prepareQuery(BaseRequest $request): array
    {
        $request->setAccessToken($this->token);

        $arr = $request->toArray();
        if (isset($arr['content'])) {
            $arr['content'] = json_encode($arr['content']);
        }

        if (isset($arr['fields'])) {
            $arr['fields'] = $this->serializeFields($arr['fields']);
        }

        return ['query' => $arr];
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
