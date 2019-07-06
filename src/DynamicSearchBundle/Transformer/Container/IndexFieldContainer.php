<?php

namespace DynamicSearchBundle\Transformer\Container;

class IndexFieldContainer implements IndexFieldContainerInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @internal
     *
     * @var string
     */
    protected $name;

    /**
     * @internal
     *
     * @var string
     */
    protected $indexType;

    /**
     * @param string $name
     * @param string $indexType
     * @param mixed  $data
     */
    public function __construct(string $name, string $indexType, $data)
    {
        $this->name = $name;
        $this->indexType = $indexType;
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexType()
    {
        return $this->indexType;
    }
}
