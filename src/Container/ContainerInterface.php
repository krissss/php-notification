<?php

namespace Kriss\Notification\Container;

interface ContainerInterface extends \Psr\Container\ContainerInterface
{
    /**
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []);

    /**
     * @param string $abstract
     * @param $concrete
     * @return mixed
     */
    public function singleton(string $abstract, $concrete = null);
}