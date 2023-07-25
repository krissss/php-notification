<?php

namespace Kriss\Notification\Integrations\Laravel;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class Notification extends \Kriss\Notification\Integrations\PHP\Notification
{
    protected static function getLocalConfig(): array
    {
        return Config::get('php-notification', []);
    }

    protected static function getDefaultLogger(?string $channel): LoggerInterface
    {
        return Log::channel($channel);
    }

    protected static function getDefaultCache(?string $driver): CacheInterface
    {
        return Cache::store($driver);
    }
}
