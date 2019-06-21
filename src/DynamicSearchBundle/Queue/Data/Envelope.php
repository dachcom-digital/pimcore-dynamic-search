<?php

namespace DynamicSearchBundle\Queue\Data;

class Envelope
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var string
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $id
     * @param string $contextName
     * @param string $dispatcher
     * @param array  $options
     */
    public function __construct(string $id, string $contextName, string $dispatcher, array $options)
    {
        $this->id = $id;
        $this->contextName = $contextName;
        $this->dispatcher = $dispatcher;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}