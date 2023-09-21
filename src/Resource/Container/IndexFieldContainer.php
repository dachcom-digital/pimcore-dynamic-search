<?php

namespace DynamicSearchBundle\Resource\Container;

class IndexFieldContainer implements IndexFieldContainerInterface
{
    public function __construct(protected string $name, protected string $indexType, protected mixed $data)
    {
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
