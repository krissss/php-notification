<?php

namespace Kriss\Notification\Services;

final class RateLimiter
{
    protected Cache $cache;
    protected array $config = [
        'key' => null,
        'maxAttempts' => null,
        'decaySeconds' => null,
    ];

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function withConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function attempt(): bool
    {
        if (!$this->config['key'] || !$this->config['maxAttempts'] || !$this->config['decaySeconds']) {
            throw new \InvalidArgumentException(self::class.' config error');
        }

        $cacheKey = md5(serialize([self::class, $this->config['key']]));
        $value = (int) $this->cache->get($cacheKey, 0);
        if ($value >= $this->config['maxAttempts']) {
            return false;
        }
        $this->cache->set($cacheKey, $value + 1, $this->config['decaySeconds']);

        return true;
    }
}
