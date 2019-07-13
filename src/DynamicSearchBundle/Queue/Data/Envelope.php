<?php

namespace DynamicSearchBundle\Queue\Data;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

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
    protected $dispatchType;

    /**
     * @var ResourceMetaInterface[]
     */
    protected $resourceMetaStack;

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
     * @param string $dispatchType
     * @param array  $resourceMetaStack
     * @param array  $options
     */
    public function __construct(string $id, string $contextName, string $dispatchType, array $resourceMetaStack, array $options)
    {
        $this->id = $id;
        $this->contextName = $contextName;
        $this->dispatchType = $dispatchType;
        $this->resourceMetaStack = $resourceMetaStack;
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
    public function getDispatchType()
    {
        return $this->dispatchType;
    }

    /**
     * @return array|ResourceMetaInterface[]
     */
    public function getResourceMetaStack()
    {
        return $this->resourceMetaStack;
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
