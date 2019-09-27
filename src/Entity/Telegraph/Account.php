<?php

namespace Ig0rbm\Memo\Entity\Telegraph;

use Symfony\Component\Validator\Constraints as Assert;

class Account
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    private $shortName;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    private $authorName;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    private $authorUrl;

    /**
     * @Assert\Type("string")
     *
     * @var string|null
     */
    private $accessToken;

    /**
     * @Assert\Type("string")
     *
     * @var string|null
     */
    private $authUrl;

    /**
     * @Assert\Type("integer")
     *
     * @var int|null
     */
    private $pageCount;

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
