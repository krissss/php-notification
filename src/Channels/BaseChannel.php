<?php

namespace Kriss\Notification\Channels;

use Kriss\Notification\Channels\Traits\TemplateSupport;
use Kriss\Notification\Exceptions\ChannelNotEnableException;
use Kriss\Notification\Exceptions\RateLimitReachException;
use Kriss\Notification\Factory;
use Kriss\Notification\Services\RateLimiter;

abstract class BaseChannel
{
    use TemplateSupport;

    protected array $config = [];
    private ?Factory $factory = null;

    public function __construct()
    {
        $this->config = array_merge_recursive([
            'enable' => true,
            'rate_limit' => [
                'key' => '',
                'maxAttempts' => 1,
                'decaySeconds' => 60,
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

    final public function withRateLimit(array $config = []): self
    {
        $this->config['rate_limit'] = array_merge($this->config['rate_limit'], $config);

        return $this;
    }

    final protected function make(string $abstract, array $parameters = [])
    {
        return $this->factory->getContainer()->make($abstract, $parameters);
    }

    final protected function wrapSendCallback(\Closure $send)
    {
        if (!$this->config['enable']) {
            return new ChannelNotEnableException();
        }

        $rateLimiter = null;
        if ($this->config['rate_limit']['key']) {
            // 存在默认的限流器
            $rateLimiter = $this
                ->make(RateLimiter::class)
                ->withConfig($this->config['rate_limit']);
        }
        if ($rateLimiter && !$rateLimiter->attempt()) {
            return new RateLimitReachException();
        }

        try {
            return \call_user_func($send);
        } catch (\Throwable $e) {
            if ($this->factory) {
                return $this->factory->handleException($e);
            }
            throw $e;
        }
    }
}
