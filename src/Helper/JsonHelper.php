<?php

namespace Kriss\Notification\Helper;

final class JsonHelper
{
    public static function encode(array $value): string
    {
        return json_encode($value, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
    }

    public static function decode(string $json): array
    {
        return json_decode($json, true);
    }
}
