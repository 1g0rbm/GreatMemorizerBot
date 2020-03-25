<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Tests;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;

class CacheAdapter implements AdapterInterface
{
    /** @var CacheItemInterface[] */
    private array $storage = [];

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        return $this->storage[$key] ?? new CacheItem();
    }

    public function save(CacheItemInterface $item)
    {
        $this->storage[$item->getKey()] = $item;
    }

    public function clear()
    {
        // TODO: Implement clear() method.
    }

    public function commit()
    {
        // TODO: Implement commit() method.
    }

    public function deleteItem($key)
    {
        // TODO: Implement deleteItem() method.
    }

    public function deleteItems(array $keys)
    {
        // TODO: Implement deleteItems() method.
    }

    public function getItems(array $keys = [])
    {
        // TODO: Implement getItems() method.
    }

    public function hasItem($key)
    {
        // TODO: Implement hasItem() method.
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        // TODO: Implement saveDeferred() method.
    }
}
