<?php

namespace Kriss\Notification\Helper;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory as GuzzleHttpFactory;
use Kriss\Notification\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class GuzzleHttpClientHelper
{
    public static function register(ContainerInterface $container)
    {
        if (!class_exists(GuzzleClient::class)) {
            return;
        }

        if (!$container->has(ClientInterface::class)) {
            $container->singleton(ClientInterface::class, GuzzleClient::class);
        }
        if (!$container->has(RequestFactoryInterface::class)) {
            $container->singleton(RequestFactoryInterface::class, GuzzleHttpFactory::class);
        }
        if (!$container->has(StreamFactoryInterface::class)) {
            $container->singleton(StreamFactoryInterface::class, GuzzleHttpFactory::class);
        }
    }
}