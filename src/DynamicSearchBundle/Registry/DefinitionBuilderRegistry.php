<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class DefinitionBuilderRegistry implements DefinitionBuilderRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function registerDocumentDefinition(DocumentDefinitionBuilderInterface $service): void
    {
        $this->registryStorage->store($service, DocumentDefinitionBuilderInterface::class, 'documentDefinitionBuilder', get_class($service));
    }

    public function registerFilterDefinition(DocumentDefinitionBuilderInterface $service): void
    {
        $this->registryStorage->store($service, FilterDefinitionBuilderInterface::class, 'filterDefinitionBuilder', get_class($service));
    }

    public function getAllDocumentDefinitionBuilder(): array
    {
        return $this->registryStorage->getByNamespace('documentDefinitionBuilder');
    }

    public function getAllFilterDefinitionBuilder(): array
    {
        return $this->registryStorage->getByNamespace('filterDefinitionBuilder');
    }
}
