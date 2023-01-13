<?php

namespace Kriss\Notification;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Kriss\Notification\Container\ContainerInterface;
use Kriss\Notification\Container\LaravelContainer;
use Psr\Container\ContainerInterface as PSRContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @mixin ContainerInterface
 */
final class Container
{
    private ?ContainerInterface $container;

    /**
     * @param ContainerInterface|PSRContainerInterface $container
     */
    public function __construct($container = null)
    {
        if ($container === null) {
            // 自动发现支持的 container
            if (class_exists(\Illuminate\Container\Container::class)) {
                $container = \Illuminate\Container\Container::getInstance();
            }
        }
        if ($container instanceof \Illuminate\Container\Container) {
            $container = new LaravelContainer($container);
        }
        $this->container = $container;

        $this->discoverHttpClient();
    }

    public function __call($name, $arguments)
    {
        return $this->container->{$name}(...$arguments);
    }

    private function discoverHttpClient()
    {
        if (!$this->container->has(ClientInterface::class)) {
            $this->container->singleton(ClientInterface::class, fn() => Psr18ClientDiscovery::find());
        }
        if (!$this->container->has(RequestFactoryInterface::class)) {
            $this->container->singleton(RequestFactoryInterface::class, fn() => Psr17FactoryDiscovery::findRequestFactory());
        }
        if (!$this->container->has(StreamFactoryInterface::class)) {
            $this->container->singleton(StreamFactoryInterface::class, fn() => Psr17FactoryDiscovery::findStreamFactory());
        }
    }
}