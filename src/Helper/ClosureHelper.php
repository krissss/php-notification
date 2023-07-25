<?php

namespace Kriss\Notification\Helper;

class ClosureHelper
{
    public static function make($callback, $default = null, ...$args)
    {
        if ($callback instanceof \Closure) {
            return \call_user_func($callback, ...$args);
        }
        if (\is_string($callback) && class_exists($callback)) {
            $obj = new $callback(...$args);

            return $obj();
        }

        return $callback ?: $default;
    }
}
