<?php

namespace DynamicSearchBundle\Resource\Container;

class OptionFieldContainer implements OptionFieldContainerInterface
{
    protected mixed $data;

    /**
     * @internal
     */
    protected string $name;

    /**
     * @internal
     */
    protected string $indexType;

    public function __construct(string $name, mixed $data)
    {
        $this->name = $name;
        $this->data = $data;
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
