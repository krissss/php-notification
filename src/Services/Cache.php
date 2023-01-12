<?php

namespace Kriss\Notification\Services;

use Psr\SimpleCache\CacheInterface;

final class Cache
{
    protected CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function set(string $key, $value, $ttl = null)
    {
        $this->cache->set($key, $value, $ttl);
    }

    public function get(string $key)
    {
        return $this->cache->get($key);
    }
}