<?php

namespace Kriss\Notification\Templates;

use Kriss\Notification\Factory;

abstract class BaseTemplate
{
    public bool $useMarkdown = true;
    private ?Factory $factory = null;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function withFactory(Factory $factory): self
    {
        $this->factory = $factory;

        return $this;
    }

    public function __toString()
    {
        $basic = fn () => $this->toString();
        if ($this->factory) {
            $result = $this->factory->handleTemplate($this, $basic);
            if (\is_string($result)) {
                return $result;
            }
        }

        return $basic();
    }

    abstract protected function toString(): string;
}
