<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Document\IndexDocumentDefinitionBuilderInterface;

class DocumentDefinitionBuilderRegistry implements DocumentDefinitionBuilderRegistryInterface
{
    /**
     * @var array
     */
    protected $definitionBuilder;

    /**
     * @param        $service
     * @param string $identifier
     */
    public function register($service, string $identifier)
    {
        if (!in_array(IndexDocumentDefinitionBuilderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), IndexDocumentDefinitionBuilderInterface::class,
                    implode(', ', class_implements($service)))
            );
        }

        $this->definitionBuilder[$identifier] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $identifier)
    {
        return isset($this->definitionBuilder[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $identifier)
    {
        if (!$this->has($identifier)) {
            throw new \Exception('"' . $identifier . '" Index Document Definition Builder does not exist');
        }

        return $this->definitionBuilder[$identifier];
    }
}