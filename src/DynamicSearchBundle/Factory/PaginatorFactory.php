<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Document\Definition\OutputDocumentDefinitionInterface;
use DynamicSearchBundle\Paginator\AdapterInterface;
use DynamicSearchBundle\Paginator\PaginatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PaginatorFactory implements PaginatorFactoryInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * @var string
     */
    protected $paginatorClass;

    /**
     * @param SerializerInterface $serializer
     * @param string              $paginatorClass
     */
    public function __construct(SerializerInterface $serializer, string $paginatorClass)
    {
        $this->serializer = $serializer;
        $this->paginatorClass = $paginatorClass;

    }

    /**
     * {@inheritDoc}
     */
    public function create($adapterData, string $adapterClass, string $contextName, string $outputChannelName, OutputDocumentDefinitionInterface $outputDocumentDefinition)
    {
        $paginatorClassName = $this->paginatorClass;

        /** @var AdapterInterface $adapter */
        $adapter = new $adapterClass($adapterData);
        $adapter->setSerializer($this->serializer);
        $adapter->setContextName($contextName);
        $adapter->setOutputChannelName($outputChannelName);
        $adapter->setOutputDocumentDefinition($outputDocumentDefinition);

        /** @var PaginatorInterface $paginator */
        $paginator = new $paginatorClassName($adapter);

        return $paginator;

    }
}