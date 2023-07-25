<?php

namespace Kriss\Notification\Container;

use Illuminate\Container\Container;

final class LaravelContainer implements ContainerInterface
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function singleton(string $abstract, $concrete = null)
    {
        $this->container->singleton($abstract, $concrete);
    }

    public function get($id)
    {
        return $this->container->get($id);
    }

    public function has($id)
    {
        return $this->container->has($id);
    }

    public function make(string $abstract, array $parameters = [])
    {
        return $this->container->make($abstract, $parameters);
    }
}
