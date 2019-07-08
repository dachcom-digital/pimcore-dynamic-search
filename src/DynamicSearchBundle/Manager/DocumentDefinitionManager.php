<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Registry\DocumentDefinitionBuilderRegistryInterface;

class DocumentDefinitionManager implements DocumentDefinitionManagerInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var DocumentDefinitionBuilderRegistryInterface
     */
    protected $documentDefinitionBuilderRegistry;

    /**
     * @param ConfigurationInterface                     $configuration
     * @param DocumentDefinitionBuilderRegistryInterface $documentDefinitionBuilderRegistry
     */
    public function __construct(
        ConfigurationInterface $configuration,
        DocumentDefinitionBuilderRegistryInterface $documentDefinitionBuilderRegistry
    ) {
        $this->configuration = $configuration;
        $this->documentDefinitionBuilderRegistry = $documentDefinitionBuilderRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentDefinitionBuilder(ContextDataInterface $contextData)
    {
        $definitionBuilderName = $contextData->getDocumentDefinitionBuilderName();

        if (is_null($definitionBuilderName)) {
            return null;
        }

        if (!$this->documentDefinitionBuilderRegistry->has($definitionBuilderName)) {
            return null;
        }

        $definitionBuilder = $this->documentDefinitionBuilderRegistry->get($definitionBuilderName);

        return $definitionBuilder;
    }
}
