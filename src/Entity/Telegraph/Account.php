<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Telegraph;

use Ig0rbm\Memo\Service\Telegraph\Request\GetAccount;
use Symfony\Component\Validator\Constraints as Assert;

class Account
{
    /**
     * @Assert\Type("string")
     */
    private ?string $shortName = null;

    /**
     * @Assert\Type("string")
     */
    private ?string $authorName = null;

    /**
     * @Assert\Type("string")
     */
    private ?string $authorUrl = null;

    /**
     * @Assert\Type("string")
     */
    private ?string $accessToken = null;

    /**
     * @Assert\Type("string")
     */
    private ?string $authUrl = null;

    public static function createFromTelegraphResponse(array $response): self
    {
        $acc = new self();

        if (isset($response[GetAccount::FIELD_AUTHOR_NAME])) {
            $acc->setAuthorName($response[GetAccount::FIELD_AUTHOR_NAME]);
        }

        if (isset($response[GetAccount::FIELD_SHORT_NAME])) {
            $acc->setShortName($response[GetAccount::FIELD_SHORT_NAME]);
        }

        if (isset($response[GetAccount::FIELD_AUTHOR_URL])) {
            $acc->setAuthorUrl($response[GetAccount::FIELD_AUTHOR_URL]);
        }

        if (isset($response[GetAccount::FIELD_AUTH_URL])) {
            $acc->setAuthUrl($response[GetAccount::FIELD_AUTH_URL]);
        }

        if (isset($response[GetAccount::FIELD_PAGE_COUNT])) {
            $acc->setPageCount($response[GetAccount::FIELD_PAGE_COUNT]);
        }

        return $acc;
    }

    /**
     * @Assert\Type("integer")
     */
    private ?int $pageCount = null;

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function setAuthorName(string $authorName): void
    {
        $this->authorName = $authorName;
    }

    public function getAuthorUrl(): string
    {
        return $this->authorUrl;
    }

    public function setAuthorUrl(string $authorUrl): void
    {
        $this->authorUrl = $authorUrl;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getAuthUrl(): ?string
    {
        return $this->authUrl;
    }

    public function setAuthUrl(?string $authUrl): void
    {
        $this->authUrl = $authUrl;
    }

    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    public function setPageCount(?int $pageCount): void
    {
        $this->pageCount = $pageCount;
    }
}
