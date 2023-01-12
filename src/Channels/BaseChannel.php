<?php

namespace Kriss\Notification\Channels;

abstract class BaseChannel
{
    protected array $config = [];

    public function withConfig(array $config): self
    {
        $this->config = array_replace_recursive($this->config, $config);
        return $this;
    }
}