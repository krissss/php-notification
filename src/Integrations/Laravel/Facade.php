<?php

namespace Kriss\Notification\Integrations\Laravel;

use Kriss\Notification\Factory;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
