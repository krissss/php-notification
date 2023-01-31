<?php

namespace Kriss\Notification\Templates;

abstract class BaseTemplate
{
    protected bool $useMarkdown = true;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    abstract public function __toString();

    public function getUseMarkdown(): bool
    {
        return $this->useMarkdown;
    }

    public function setUseMarkdown(bool $is): self
    {
        $this->useMarkdown = $is;
        return $this;
    }
}
