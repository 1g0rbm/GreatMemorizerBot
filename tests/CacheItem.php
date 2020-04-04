<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests;

use Symfony\Contracts\Cache\ItemInterface;
use DateTimeImmutable;

class CacheItem implements ItemInterface
{
    private bool $isHit;

    private string $key;

    /** @var mixed */
    private $data;

    private ?DateTimeImmutable $expires;

    public function __construct(string $key, bool $isHit = false)
    {
        $this->isHit = $isHit;
        $this->key   = $key;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter($time)
    {
        $this->expires = $time;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt($expiration)
    {
        $this->expires = $expiration;
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata(): array
    {
        // TODO: Implement getMetadata() method.
    }

    /**
     * @inheritDoc
     */
    public function isHit()
    {
        return $this->isHit;
    }

    /**
     * @inheritDoc
     */
    public function set($value)
    {
        $this->data = $value;
    }

    /**
     * @inheritDoc
     */
    public function tag($tags): ItemInterface
    {
        // TODO: Implement tag() method.
    }
}
