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
     * @var string
     */
    protected $resourceType;

    /**
     * @var int
     */
    protected $resourceId;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $id
     * @param string $contextName
     * @param string $dispatcher
     * @param string $resourceType
     * @param int    $resourceId
     * @param array  $options
     */
    public function __construct(string $id, string $contextName, string $dispatcher, string $resourceType, int $resourceId, array $options)
    {
        $this->id = $id;
        $this->contextName = $contextName;
        $this->dispatcher = $dispatcher;
        $this->resourceType = $resourceType;
        $this->resourceId = $resourceId;
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
     * @return string
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * @return int
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}