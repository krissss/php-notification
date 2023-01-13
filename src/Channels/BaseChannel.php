<?php

namespace Kriss\Notification\Channels;

use Closure;
use Kriss\Notification\Factory;
use Throwable;

abstract class BaseChannel
{
    protected array $config = [];
    private ?Factory $factory = null;

    public function withConfig(array $config): self
    {
        $this->config = array_replace_recursive($this->config, $config);
        return $this;
    }

    public function withFactory(Factory $factory): self
    {
        $this->factory = $factory;
        return $this;
    }

    protected function wrapSendCallback(Closure $send, $failedResult = false)
    {
        try {
            return call_user_func($send);
        } catch (Throwable $e) {
            if ($this->factory) {
                $this->factory->handleException($e);
            } else {
                throw $e;
            }
            return $failedResult;
        }
    }
}