<?php

namespace DynamicSearchBundle\Resource\Container;

class OptionFieldContainer implements OptionFieldContainerInterface
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
     * @param mixed  $data
     */
    public function __construct(string $name, $data)
    {
        $this->name = $name;
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
    public function getName()
    {
        return $this->name;
    }
}
