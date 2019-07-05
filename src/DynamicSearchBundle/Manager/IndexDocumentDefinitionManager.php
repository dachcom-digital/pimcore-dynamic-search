<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Registry\DocumentDefinitionBuilderRegistryInterface;

class IndexDocumentDefinitionManager implements IndexDocumentDefinitionManagerInterface
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
    public function getIndexDocumentDefinitionBuilder(ContextDataInterface $contextData)
    {
        $definitionBuilderName = $contextData->getIndexDocumentDefinitionBuilderName();

        if (is_null($definitionBuilderName)) {
            return null;
        }

        if (!$this->documentDefinitionBuilderRegistry->has($definitionBuilderName)) {
            return null;
        }

        $definitionBuilder = $this->documentDefinitionBuilderRegistry->get($definitionBuilderName);

        try {
            $dataProviderOptions = $contextData->getIndexDocumentDefinitionOptions($definitionBuilder);
        } catch (ContextConfigurationException $e) {
            return null;
        }

        $definitionBuilder->setOptions($dataProviderOptions);

        return $definitionBuilder;
    }
}
