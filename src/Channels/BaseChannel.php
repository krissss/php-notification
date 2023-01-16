<?php

namespace Kriss\Notification\Channels;

use Closure;
use Kriss\Notification\Exceptions\RateLimitReachException;
use Kriss\Notification\Factory;
use Kriss\Notification\Services\RateLimiter;
use Throwable;

abstract class BaseChannel
{
    protected array $config = [];
    private ?Factory $factory = null;
    private ?RateLimiter $rateLimiter = null;

    public function __construct()
    {
        $this->config = array_merge_recursive([
            'rate_limit' => [
                'key' => '',
                'maxAttempts' => 0,
                'decaySeconds' => 0,
            ],
        ], $this->config);
    }

    final public function withConfig(array $config): self
    {
        $this->config = array_replace_recursive($this->config, $config);
        return $this;
    }

    final public function withFactory(Factory $factory): self
    {
        $this->factory = $factory;
        return $this;
    }

    final public function withRateLimit(string $key, int $maxAttempts, int $decaySeconds): self
    {
        $this->rateLimiter = $this->factory->getContainer()
            ->make(RateLimiter::class)
            ->withConfig([
                'key' => $key,
                'maxAttempts' => $maxAttempts,
                'decaySeconds' => $decaySeconds,
            ]);
        return $this;
    }

    final protected function wrapSendCallback(Closure $send)
    {
        if (!$this->rateLimiter && $this->config['rate_limit']['key']) {
            // 存在默认的限流器
            $this->withRateLimit(
                $this->config['rate_limit']['key'],
                $this->config['rate_limit']['maxAttempts'],
                $this->config['rate_limit']['decaySeconds'],
            );
        }
        if ($this->rateLimiter && !$this->rateLimiter->attempt()) {
            return new RateLimitReachException();
        }

        try {
            return call_user_func($send);
        } catch (Throwable $e) {
            if ($this->factory) {
                return $this->factory->handleException($e);
            }
            throw $e;
        }
    }
}