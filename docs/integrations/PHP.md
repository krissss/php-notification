# PHP

## 配置

复制 [notification.php](../../config/notification.php) 到项目下任意地方

修改 `notification.php` 中的相关配置

## 使用方式

新建类如下：

```php
<?php

namespace App\Components;

use Kriss\Notification\Channels;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;use Psr\SimpleCache\CacheInterface;

/**
 * @method static Channels\WeWorkBotChannel weWorkBot()
 * @method static Channels\WeWorkAppChannel weWorkApp()
 * @method static Channels\MailerChannel mailer()
 */
class Notification extends \Kriss\Notification\Integrations\PHP\Notification
{
    protected static function getLocalConfig(): array
    {
        return require __DIR__ . '/../config/notification.php'; // 修改为真实的路径
    }
    
    protected static function getDefaultLogger(?string $channel): LoggerInterface
    {
        // 如果需要日志，提供 LoggerInterface 实现
        //return logger();
    }

    protected static function getDefaultCache(?string $driver): CacheInterface
    {
        // 提供 CacheInterface 实现
        //return cache();
    }
}
```

使用

```php
use App\Components\Notification;

Notification::weWorkBot()->sendText('test');
```
