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

    /**
     * @inheritDoc
     */
    public function singleton(string $abstract, $concrete = null)
    {
        $this->container->singleton($abstract, $concrete);
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * @inheritDoc
     */
    public function make(string $abstract, array $parameters = [])
    {
        return $this->container->make($abstract, $parameters);
    }
}