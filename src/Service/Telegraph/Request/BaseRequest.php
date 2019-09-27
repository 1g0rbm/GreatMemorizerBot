<?php

namespace Ig0rbm\Memo\Service\Telegraph\Request;

abstract class BaseRequest
{
    /** @var string */
    protected $accessToken;

    /** @var string */
    protected $returnContent;

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getReturnContent(): string
    {
        return $this->returnContent;
    }

    public function setReturnContent(string $returnContent): void
    {
        $this->returnContent = $returnContent;
    }

    public function toArray(): array
    {
        $toShakeCase = function ($string) {
            preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
            $ret = $matches[0];

            foreach ($ret as &$match) {
                $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
            }

            return implode('_', $ret);
        };

        $vars = get_object_vars($this);
        $snake = [];

        foreach ($vars as $key => $var) {
            $snake[$toShakeCase($key)] = $var;
        }

        return $snake;
    }
}
