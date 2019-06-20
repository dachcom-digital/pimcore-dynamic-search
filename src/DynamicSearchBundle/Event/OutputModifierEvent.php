<?php

namespace DynamicSearchBundle\Event;

class OutputModifierEvent
{
    /**
     * @var array
     */
    protected $parameter;

    /**
     * @param array $parameter
     */
    public function __construct(array $parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setParameter($key, $value)
    {
        $this->parameter[$key] = $value;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasParameter($key)
    {
        return isset($this->parameter[$key]);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getParameter($key)
    {
        return $this->parameter[$key];
    }
}
