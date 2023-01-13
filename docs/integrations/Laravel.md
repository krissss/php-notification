# Laravel

## 配置

```bash
# 发布配置
php artisan vendor:publish --provider="Kriss\Notification\Integrations\Laravel\NotificationServiceProvider"
```

修改 `config/php-notification.php` 中的相关配置

## 常规使用方式

```php
// 使用 Facade
\PhpNotification::channel('weWorkBot')->sendText('test');

// 使用单例
\Kriss\Notification\Integrations\Laravel\Notification::instance()->channel('weWorkBot')->sendText('test');
```

## 推荐使用方式（代码提示）

新建类如下：

```php
<?php

namespace App\Components;

use Kriss\Notification\Channels;

/**
 * @method static Channels\WeWorkBotChannel weWorkBot()
 * @method static Channels\WeWorkAppChannel weWorkApp()
 * @method static Channels\MailerChannel mailer()
 */
class Notification extends \Kriss\Notification\Integrations\Laravel\Notification
{
}
```

使用

```php
use App\Components\Notification;

Notification::weWorkBot()->sendText('test');
```
