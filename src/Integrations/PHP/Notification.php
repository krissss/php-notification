<?php

namespace Kriss\Notification\Integrations\PHP;

use Kriss\Notification\Container;
use Kriss\Notification\Factory;
use Kriss\Notification\Helper\ClosureHelper;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;

class Notification
{
    protected static ?Factory $_instance = null;

    public static function instance(): Factory
    {
        if (!static::$_instance) {
            static::$_instance = static::createFactory();
        }
        return static::$_instance;
    }

    protected static function createFactory(): Factory
    {
        $config = array_replace_recursive(
            require __DIR__ . '/../../../config/notification.php',
            static::getLocalConfig(),
        );
        return new Factory(static::createContainer($config), $config);
    }

    protected static function getLocalConfig(): array
    {
        return [];
    }

    protected static function createContainer(array $config = []): Container
    {
        $container = new \Illuminate\Container\Container();

        $container->singleton(LoggerInterface::class, function () use ($config): LoggerInterface {
            $config = $config['log'];
            if (!$config['enable']) {
                return new NullLogger();
            }
            if ($config['instance']) {
                return ClosureHelper::make($config['instance']);
            }
            return static::getDefaultLogger($config['channel']);
        });
        $container->singleton(CacheInterface::class, function () use ($config): CacheInterface {
            $config = $config['cache'];
            if ($config['instance']) {
                return ClosureHelper::make($config['instance']);
            }
            return static::getDefaultCache($config['driver']);
        });

        return new Container($container);
    }

    protected static function getDefaultLogger(?string $channel): LoggerInterface
    {
        throw new \InvalidArgumentException('请提供默认的 LoggerInterface 实现');
    }

    protected static function getDefaultCache(?string $driver): CacheInterface
    {
        throw new \InvalidArgumentException('请提供默认的 CacheInterface 实现');
    }

    public static function __callStatic($name, $arguments)
    {
        return static::instance()->channel($name);
    }
}