<?php

namespace DynamicSearchBundle\Transformer\Container;

class FieldContainer implements FieldContainerInterface
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
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
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
    public function setName(string $name)
    {
        $this->name = $name;
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
    public function setIndexType(?string $indexType)
    {
        $this->indexType = $indexType;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexType()
    {
        return $this->indexType;
    }
}
