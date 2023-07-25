<?php

namespace Kriss\Notification\Exceptions;

use Psr\Http\Client\ClientExceptionInterface;

class AccessTokenGetException extends \RuntimeException implements ClientExceptionInterface
{
}
