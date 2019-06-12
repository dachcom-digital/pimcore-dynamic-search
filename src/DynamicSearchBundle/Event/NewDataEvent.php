<?php

namespace DynamicSearchBundle\Event;

use DynamicSearchBundle\Context\ContextDataInterface;
use Symfony\Component\EventDispatcher\Event;

class NewDataEvent extends Event
{
    /**
     * @var string
     */
    protected $provider;

    /**
     * @var ContextDataInterface
     */
    protected $contextData;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param string               $provider
     * @param ContextDataInterface $contextData
     * @param mixed                $data
     */
    public function __construct(string $provider, ContextDataInterface $contextData, $data)
    {
        $this->provider = $provider;
        $this->contextData = $contextData;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return ContextDataInterface
     */
    public function getContextData()
    {
        return $this->contextData;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
