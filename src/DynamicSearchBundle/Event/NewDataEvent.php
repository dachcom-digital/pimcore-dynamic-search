<?php

namespace DynamicSearchBundle\Event;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use Symfony\Contracts\EventDispatcher\Event;

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
     * @var mixed
     */
    protected $data;

    /**
     * @var string
     */
    protected $providerBehaviour;

    /**
     * @var ResourceMetaInterface
     */
    protected $resourceMeta;

    /**
     * @param string                     $contextDispatchType
     * @param string                     $contextName
     * @param mixed                      $data
     * @param string                     $providerBehaviour
     * @param ResourceMetaInterface|null $resourceMeta
     */
    public function __construct(string $contextDispatchType, string $contextName, $data, string $providerBehaviour, ?ResourceMetaInterface $resourceMeta)
    {
        $this->contextDispatchType = $contextDispatchType;
        $this->contextName = $contextName;
        $this->data = $data;
        $this->providerBehaviour = $providerBehaviour;
        $this->resourceMeta = $resourceMeta;
    }

    /**
     * @return string
     */
    public function getContextDispatchType()
    {
        return $this->contextDispatchType;
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

    /**
     * @return string
     */
    public function getProviderBehaviour()
    {
        return $this->providerBehaviour;
    }

    /**
     * @return ResourceMetaInterface|null
     */
    public function getResourceMeta()
    {
        return $this->resourceMeta;
    }
}
