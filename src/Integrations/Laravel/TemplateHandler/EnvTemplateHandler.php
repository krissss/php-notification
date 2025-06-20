<?php

namespace Kriss\Notification\Integrations\Laravel\TemplateHandler;

class EnvTemplateHandler extends \Kriss\Notification\Integrations\PHP\TemplateHandler\EnvTemplateHandler
{
    /**
     * 适用于uid 写在配置文件中且是动态值，并且生成了配置缓存
     * @var string 从常量获取，后面跟上常量名，如 UID_WITH_CONSTANT . 'APP_REQUEST_UID'
     */
    const UID_WITH_CONSTANT = '__uid_with_constant__';

    protected function getEnv(): string
    {
        return config('app.url');
    }

    protected function getUid(): string
    {
        if ($this->config['uid']) {
            $prefix = self::UID_WITH_CONSTANT;
            $prefixLen = strlen($prefix);
            if (strncmp($this->config['uid'], $prefix, $prefixLen) === 0) {
                // 获取去掉前缀后的值
                $constantName = substr($this->config['uid'], $prefixLen);
                if (defined($constantName)) {
                    return constant($constantName);
                }
            }
        }
        return parent::getUid();
    }
}
