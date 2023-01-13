<?php

namespace Kriss\Notification\Integrations\Laravel;

use Illuminate\Support\ServiceProvider;
use Kriss\Notification\Factory;

class NotificationServiceProvider extends ServiceProvider
{
    public function isDeferred()
    {
        return true;
    }

    public function boot()
    {
        $configPath = __DIR__ . '/../../../config/notification.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('php-notification.php');
        } else {
            $publishPath = base_path('config/php-notification.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
    }

    public function register()
    {
        $this->app->singleton(Factory::class, fn() => Notification::instance());
        $this->app->alias(Factory::class, 'php-notification');
    }

    public function provides()
    {
        return [
            Factory::class,
            'php-notification',
        ];
    }
}