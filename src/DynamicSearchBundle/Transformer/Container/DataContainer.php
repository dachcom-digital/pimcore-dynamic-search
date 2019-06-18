<?php

namespace DynamicSearchBundle\Transformer\Container;

class DataContainer implements DataContainerInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function hasDataAttribute($attribute)
    {
        return isset($this->data[$attribute]);
    }

    /**
     * {@inheritDoc}
     */
    public function getDataAttribute($attribute)
    {
        return $this->data[$attribute];
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }
}
