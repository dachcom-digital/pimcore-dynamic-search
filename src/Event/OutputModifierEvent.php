<?php

namespace DynamicSearchBundle\Event;

class OutputModifierEvent
{
    public function __construct(protected array $parameter)
    {
    }

    public function setParameter(string $key, mixed $value): void
    {
        $this->parameter[$key] = $value;
    }

    public function hasParameter(string $key): bool
    {
        return isset($this->parameter[$key]);
    }

    public function getParameter(string $key): mixed
    {
        return $this->parameter[$key];
    }
}
