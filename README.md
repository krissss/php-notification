# PHP 消息通知

## 安装

```bash
composer require kriss/php-notification
```

## 特性

- 支持多框架：[原生 PHP](./docs/integrations/PHP.md)、[Laravel](./docs/integrations/Laravel.md)、[Webman](./docs/integrations/Webman.md)（可扩展）
- 支持多渠道：邮件、企业微信机器人、企业微信内部应用（可扩展）
- 标准的 PSR3、PSR16、PSR11、PSR17、PSR18 接口实现
- 支持使用配置级别参数，减少实际使用时的配置参数
- 支持限流
- 支持抑制异常抛出和自定义处理异常

## 核心组件说明

### Container

默认支持 `illuminate/container`（laravel 的 container），所以如果没有请先安装

*可扩展*

### HttpClient

默认会使用 `php-http/discovery` 自动发现项目下安装的 PSR17、PSR18 实现，如果没有建议安装 `guzzlehttp/guzzle`

*注意：如果 guzzle 版本小于 7 的，需要单独安装 `nyholm/psr7`*
