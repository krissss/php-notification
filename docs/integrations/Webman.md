# Webman

## 配置

修改 `config/plugin/kriss/notification/notification.php` 中的相关配置

## 常规使用方式

```php
// 使用单例
\Kriss\Notification\Integrations\Webman\Notification::instance()->channel('weWorkBot')->sendText('test');
```

## 推荐使用方式（代码提示）

新建类如下：

```php
<?php

namespace support\facade;

use Kriss\Notification\Channels;

/**
 * @method static Channels\WeWorkBotChannel weWorkBot()
 * @method static Channels\WeWorkAppChannel weWorkApp()
 * @method static Channels\MailerChannel mailer()
 */
class Notification extends \Kriss\Notification\Integrations\Webman\Notification
{
}
```

使用

```php
use support\facade\Notification;

Notification::weWorkBot()->sendText('test');
```
