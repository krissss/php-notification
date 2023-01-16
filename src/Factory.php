<?php

namespace Kriss\Notification;

use Closure;
use Kriss\Notification\Channels\BaseChannel;
use Kriss\Notification\Services\Logger;
use Throwable;

final class Factory
{
    private Container $container;
    private array $config = [
        'default' => 'default',
        'channels' => [],
    ];
    private array $channels = [];

    public function __construct(Container $container = null, array $config = [])
    {
        $this->container = $container ?? new Container();
        $this->config = array_merge($this->config, $config);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function channel(string $name = null): BaseChannel
    {
        $name = $name ?? $this->config['default'];
        if (isset($this->channels[$name])) {
            return $this->channels[$name];
        }
        return $this->channels[$name] = $this->createChannel($this->config['channels'][$name]);
    }

    public function handleException(Throwable $e): void
    {
        if ($this->config['exception']['handler'] instanceof Closure) {
            call_user_func($this->config['exception']['handler'], $e);
            return;
        }
        if ($this->config['exception']['throw']) {
            throw $e;
        }
        $this->container->get(Logger::class)->error($e);
    }

    private function createChannel(array $config): BaseChannel
    {
        if (!isset($config['class'])) {
            throw new \InvalidArgumentException('class 必须');
        }
        $class = $config['class'];
        unset($config['class']);
        $obj = $this->container->make($class);
        if (!$obj instanceof BaseChannel) {
            throw new \InvalidArgumentException('必须是 BaseChannel');
        }
        return $obj
            ->withConfig($config)
            ->withFactory($this);
    }
}
