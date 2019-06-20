<?php

namespace DynamicSearchBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class NewDataEvent extends Event
{
    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param string $contextName
     * @param mixed  $data
     */
    public function __construct(string $contextName, $data)
    {
        $this->contextName = $contextName;
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
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
