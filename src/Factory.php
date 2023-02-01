<?php

namespace Kriss\Notification;

use Closure;
use Kriss\Notification\Channels\BaseChannel;
use Kriss\Notification\Helper\ClosureHelper;
use Kriss\Notification\Services\Logger;
use Throwable;

final class Factory
{
    private Container $container;
    private array $config = [
        'default' => 'default',
        'channels' => [],
        'exception' => [
            'handler' => null,
            'throw' => true,
        ],
        'template' => [
            'handler' => null,
            'extra_info' => [],
        ],
    ];
    private array $channels = [];

    public function __construct(Container $container = null, array $config = [])
    {
        $this->container = $container ?? new Container();
        $this->config = array_merge($this->config, $config);

        if ($channels = ClosureHelper::make($this->config['channels'])) {
            $this->config['channels'] = $channels;
        }
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

    public function handleException(Throwable $e): ?Throwable
    {
        $handler = $this->config['exception']['handler'];
        $result = ClosureHelper::make($handler, null, $e);
        if ($result !== null) {
            return $result;
        }
        if ($this->config['exception']['throw']) {
            throw $e;
        }
        $this->container->get(Logger::class)->error($e);
        return $e;
    }

    public function handleTemplate(Templates\BaseTemplate $template, Closure $toString)
    {
        $handler = $this->config['template']['handler'];
        $extraInfo = $this->config['template']['extra_info'];
        $result = ClosureHelper::make($handler, '__DEFAULT_VALUE__', $template, $toString, $extraInfo);
        if ($result !== '__DEFAULT_VALUE__') {
            return $result;
        }
        return false;
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
