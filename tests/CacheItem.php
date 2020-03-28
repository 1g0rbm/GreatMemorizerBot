<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests;

use Symfony\Contracts\Cache\ItemInterface;
use DateTimeImmutable;

class CacheItem implements ItemInterface
{
    private bool $isHit;

    private string $key;

    private string $data;

    private ?DateTimeImmutable $expires;

    public function __construct(string $key, bool $isHit = false)
    {
        $this->isHit = $isHit;
        $this->key   = $key;
    }

    public function expiresAfter($time)
    {
        $this->expires = $time;
    }

    public function expiresAt($expiration)
    {
        $this->expires = $expiration;
    }

    public function get()
    {
        return $this->data;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getMetadata(): array
    {
        // TODO: Implement getMetadata() method.
    }

    public function isHit()
    {
        return $this->isHit;
    }

    public function set($value)
    {
        $this->data = $value;
    }

    public function tag($tags): ItemInterface
    {
        // TODO: Implement tag() method.
    }
}