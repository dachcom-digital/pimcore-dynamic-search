<?php

namespace DynamicSearchBundle\OutputChannel\Query\Result;

class RawResult implements RawResultInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var int
     */
    protected $hitCount;

    /**
     * @var array
     */
    protected $parameter;

    public function __construct()
    {
        $this->hitCount = 0;
        $this->parameter = [];
    }

    /**
     * {@inheritDoc}
     */
    public function setData($data)
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
    public function setHitCount(int $hitCount)
    {
        $this->hitCount = $hitCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getHitCount()
    {
        return $this->hitCount;
    }

    /**
     * {@inheritDoc}
     */
    public function addParameter($key, $value)
    {
        $this->setParameter($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function setParameter($key, $value)
    {
        $this->parameter[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function hasParameter($key)
    {
        return isset($this->parameter[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParameter($key)
    {
        return $this->parameter[$key];
    }
}
