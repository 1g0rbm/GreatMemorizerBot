<?php

namespace Ig0rbm\Memo\Service\Telegraph\Request;

use Symfony\Component\Validator\Constraints as Assert;

class GetAccount extends BaseRequest
{
    public const FIELD_SHORT_NAME  = 'short_name';
    public const FIELD_AUTHOR_NAME = 'author_name';
    public const FIELD_AUTHOR_URL  = 'author_url';
    public const FIELD_AUTH_URL    = 'auth_url';
    public const FIELD_PAGE_COUNT  = 'page_count';

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    protected $accessToken;

    /**
     * @Assert\NotBlank
     * @Assert\Type("array")
     *
     * @var string[]
     */
    protected $fields;

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param string[] $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }
}
