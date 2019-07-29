<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;

class DefinitionBuilderRegistry implements DefinitionBuilderRegistryInterface
{
    /**
     * @var array
     */
    protected $documentDefinitionBuilder;

    /**
     * @var array
     */
    protected $filterDefinitionBuilder;

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

        $this->documentDefinitionBuilder[] = $service;
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

        $this->filterDefinitionBuilder[] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllDocumentDefinitionBuilder()
    {
        return !is_array($this->documentDefinitionBuilder) ? [] : $this->documentDefinitionBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllFilterDefinitionBuilder()
    {
        return !is_array($this->filterDefinitionBuilder) ? [] : $this->filterDefinitionBuilder;
    }
}
