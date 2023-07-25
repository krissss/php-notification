<?php

namespace Kriss\Notification\Integrations\Laravel\TemplateHandler;

class EnvTemplateHandler extends \Kriss\Notification\Integrations\PHP\TemplateHandler\EnvTemplateHandler
{
    protected function getEnv(): string
    {
        return config('app.url');
    }
}
