<?php

namespace Kriss\Notification\Services;

use Kriss\Notification\Helper\JsonHelper;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Logger
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function info($message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        if ($this->logger instanceof NullLogger) {
            return;
        }

        if (is_callable($message)) {
            $message = call_user_func($message);
        }
        if (is_array($message)) {
            $message = JsonHelper::encode($message);
        }
        $this->logger->log($level, $message, $context);
    }
}