<?php

namespace Kriss\Notification;

use Kriss\Notification\Channels\BaseChannel;

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

    public function channel(string $name = null): BaseChannel
    {
        $name = $name ?? $this->config['default'];
        if (isset($this->channels[$name])) {
            return $this->channels[$name];
        }
        return $this->channels[$name] = $this->createChannel($this->config['channels'][$name]);
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
        return $obj->withConfig($config);
    }
}
