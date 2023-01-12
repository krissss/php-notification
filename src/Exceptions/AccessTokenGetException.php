<?php

namespace Kriss\Notification\Exceptions;

use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

class AccessTokenGetException extends RuntimeException implements ClientExceptionInterface
{
}