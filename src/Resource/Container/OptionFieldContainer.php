<?php

namespace DynamicSearchBundle\Resource\Container;

class OptionFieldContainer implements OptionFieldContainerInterface
{
    public function __construct(protected string $name, protected mixed $data)
    {
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
