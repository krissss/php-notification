<?php

namespace Kriss\Notification\Container;

interface ContainerInterface extends \Psr\Container\ContainerInterface
{
    public function make(string $abstract, array $parameters = []);

    public function singleton(string $abstract, $concrete = null);
}
