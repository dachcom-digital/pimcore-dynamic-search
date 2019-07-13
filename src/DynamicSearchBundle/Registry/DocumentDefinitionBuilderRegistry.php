<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;

class DocumentDefinitionBuilderRegistry implements DocumentDefinitionBuilderRegistryInterface
{
    /**
     * @var array
     */
    protected $definitionBuilder;

    /**
     * @param DocumentDefinitionBuilderInterface $service
     */
    public function register($service)
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

        $this->definitionBuilder[] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllDocumentDefinitionBuilder()
    {
        return !is_array($this->definitionBuilder) ? [] : $this->definitionBuilder;
    }
}
