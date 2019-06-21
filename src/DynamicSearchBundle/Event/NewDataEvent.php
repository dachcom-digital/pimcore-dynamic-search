<?php

namespace DynamicSearchBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class NewDataEvent extends Event
{
    /**
     * @var string
     */
    protected $contextDispatchType;

    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var array
     */
    protected $runtimeOptions;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param string $contextDispatchType
     * @param string $contextName
     * @param mixed  $data
     * @param array  $runtimeOptions
     */
    public function __construct(string $contextDispatchType, string $contextName, $data, array $runtimeOptions = [])
    {
        $this->contextDispatchType = $contextDispatchType;
        $this->contextName = $contextName;
        $this->runtimeOptions = $runtimeOptions;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getContextName()
    {
        return $this->contextName;
    }

    /**
     * @return string
     */
    public function getContextDispatchType()
    {
        return $this->contextDispatchType;
    }

    /**
     * @return array
     */
    public function getRuntimeOptions()
    {
        return $this->runtimeOptions;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
