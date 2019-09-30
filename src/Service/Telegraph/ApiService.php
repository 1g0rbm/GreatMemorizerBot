<?php

namespace Ig0rbm\Memo\Service\Telegraph;

use Throwable;
use Symfony\Component\HttpFoundation\Request;
use Ig0rbm\Memo\Entity\Telegraph\Account;
use Ig0rbm\Memo\Service\Telegraph\Request\BaseRequest;
use Ig0rbm\Memo\Service\Telegraph\Request\CreatePage;
use Ig0rbm\Memo\Service\Telegraph\Request\EditPage;
use Ig0rbm\Memo\Service\Telegraph\Request\GetAccount;
use Ig0rbm\Memo\Entity\Telegraph\Page;
use Ig0rbm\Memo\Exception\Telegraph\TelegraphApiException;
use GuzzleHttp\Client;

class ApiService
{
    public const ACCOUNT_INFO = '/getAccountInfo';
    public const CREATE_PAGE  = '/createPage';
    public const EDIT_PAGE    = '/editPage';

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
        return Page::createFromTelegraphResponse($this->doRequest($request, self::CREATE_PAGE));
    }

    public function editPage(EditPage $request): Page
    {
        return Page::createFromTelegraphResponse($this->doRequest($request, self::EDIT_PAGE));
    }

    public function getAccountInfo(GetAccount $request): Account
    {
        return Account::createFromTelegraphResponse($this->doRequest($request, self::ACCOUNT_INFO));
    }

    /**
     * @return mixed[]
     */
    public function doRequest(BaseRequest $request, string $uri): array
    {
        try {
            $response = $this->client->request(
                Request::METHOD_POST,
                $uri,
                $this->prepareQuery($request)
            );
        } catch (Throwable $e) {
            throw TelegraphApiException::becauseBadRequestToTelegraph($e->getMessage());
        }

        $content = json_decode($response->getBody()->getContents(), true);
        if ($content['ok'] === false) {
            throw TelegraphApiException::becauseBadResponseFromTelegraph($content['error']);
        }

        return $content['result'];
    }

    /**
     * @return mixed[]
     */
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

        return ['form_params' => $arr];
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
