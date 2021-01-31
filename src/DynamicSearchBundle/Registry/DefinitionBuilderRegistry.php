<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class DefinitionBuilderRegistry implements DefinitionBuilderRegistryInterface
{
    /**
     * @var RegistryStorage
     */
    protected $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    /**
     * @param DocumentDefinitionBuilderInterface $service
     */
    public function registerDocumentDefinition($service)
    {
        $this->registryStorage->store($service, DocumentDefinitionBuilderInterface::class, 'documentDefinitionBuilder', get_class($service));
    }

    /**
     * @param DocumentDefinitionBuilderInterface $service
     */
    public function registerFilterDefinition($service)
    {
        $this->registryStorage->store($service, FilterDefinitionBuilderInterface::class, 'filterDefinitionBuilder', get_class($service));
    }

    /**
     * {@inheritdoc}
     */
    public function getAllDocumentDefinitionBuilder()
    {
        return $this->registryStorage->getByNamespace('documentDefinitionBuilder');
    }

    /**
     * {@inheritdoc}
     */
    public function getAllFilterDefinitionBuilder()
    {
        return $this->registryStorage->getByNamespace('filterDefinitionBuilder');
    }
}
