<?php

namespace Kriss\Notification;

use Kriss\Notification\Container\ContainerInterface;
use Kriss\Notification\Container\LaravelContainer;
use Kriss\Notification\Helper\GuzzleHttpClientHelper;
use Psr\Container\ContainerInterface as PSRContainerInterface;

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

        GuzzleHttpClientHelper::register($container);
    }

    public function __call($name, $arguments)
    {
        return $this->container->{$name}(...$arguments);
    }
}