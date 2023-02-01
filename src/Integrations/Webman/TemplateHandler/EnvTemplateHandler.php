<?php

namespace Kriss\Notification\Integrations\Webman\TemplateHandler;

class EnvTemplateHandler extends \Kriss\Notification\Integrations\PHP\TemplateHandler\EnvTemplateHandler
{
    protected function getEnv(): string
    {
        return config('app.name') . (config('app.debug') ? '(debug)' : '');
    }
}