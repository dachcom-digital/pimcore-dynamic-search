<?php

namespace DynamicSearchBundle\Resource\Container;

class IndexFieldContainer implements IndexFieldContainerInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @internal
     */
    protected string $name;

    /**
     * @internal
     */
    protected string $indexType;

    public function __construct(string $name, string $indexType, $data)
    {
        $this->name = $name;
        $this->indexType = $indexType;
        $this->data = $data;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getIndexType(): string
    {
        return $this->indexType;
    }
}
