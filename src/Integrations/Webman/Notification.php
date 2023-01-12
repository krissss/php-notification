<?php

namespace Kriss\Notification\Integrations\Webman;

use Kriss\Notification\Factory;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use support\Cache;
use support\Log;

class Notification extends \Kriss\Notification\Integrations\PHP\Notification
{
    protected static ?Factory $_instance = null;

    public static function instance(): Factory
    {
        if (!static::$_instance) {
            static::$_instance = static::createFactory();
        }
        return static::$_instance;
    }

    protected static function getLocalConfig(): array
    {
        return config('plugin.kriss.notification.notification', []);
    }

    protected static function getDefaultLogger(?string $channel): LoggerInterface
    {
        if ($channel) {
            return Log::channel($channel);
        }
        return Log::channel();
    }

    protected static function getDefaultCache(?string $driver): CacheInterface
    {
        return Cache::instance();
    }
}