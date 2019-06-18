<?php

namespace DynamicSearchBundle\Event;

use DynamicSearchBundle\Context\ContextDataInterface;
use Symfony\Component\EventDispatcher\Event;

class NewDataEvent extends Event
{
    /**
     * @var ContextDataInterface
     */
    protected $contextData;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $data
     */
    public function __construct(ContextDataInterface $contextData, $data)
    {
        $this->contextData = $contextData;
        $this->data = $data;
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
