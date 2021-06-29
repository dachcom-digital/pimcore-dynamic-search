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
     */
    protected string $name;

    public function __construct(string $name, $data)
    {
        $this->name = $name;
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
