<?php

namespace DynamicSearchBundle\Resource\Container;

class IndexFieldContainer implements IndexFieldContainerInterface
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

    public function __construct(string $name, string $indexType, mixed $data)
    {
        $this->name = $name;
        $this->indexType = $indexType;
        $this->data = $data;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getIndexType(): string
    {
        return $this->indexType;
    }
}
