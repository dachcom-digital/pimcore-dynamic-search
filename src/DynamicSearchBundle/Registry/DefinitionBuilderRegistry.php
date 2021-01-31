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
        if (!in_array(DocumentDefinitionBuilderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s needs to implement "%s", "%s" given.',
                    get_class($service),
                    DocumentDefinitionBuilderInterface::class,
                    implode(', ', class_implements($service))
                )
            );
        }

        $this->registryStorage->store($service, 'documentDefinitionBuilder', get_class($service));
    }

    /**
     * @param DocumentDefinitionBuilderInterface $service
     */
    public function registerFilterDefinition($service)
    {
        if (!in_array(FilterDefinitionBuilderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s needs to implement "%s", "%s" given.',
                    get_class($service),
                    FilterDefinitionBuilderInterface::class,
                    implode(', ', class_implements($service))
                )
            );
        }

        $this->registryStorage->store($service, 'filterDefinitionBuilder', get_class($service));
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
