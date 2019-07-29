<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinition;
use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Resolver\DocumentDefinitionResolverInterface;

class DocumentDefinitionManager implements DocumentDefinitionManagerInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var DocumentDefinitionResolverInterface
     */
    protected $documentDefinitionResolver;

    /**
     * @param ConfigurationInterface              $configuration
     * @param DocumentDefinitionResolverInterface $documentDefinitionResolver
     */
    public function __construct(
        ConfigurationInterface $configuration,
        DocumentDefinitionResolverInterface $documentDefinitionResolver
    ) {
        $this->configuration = $configuration;
        $this->documentDefinitionResolver = $documentDefinitionResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function generateDocumentDefinition(ContextDataInterface $contextData, ResourceMetaInterface $resourceMeta)
    {
        try {
            $documentDefinitionBuilderStack = $this->documentDefinitionResolver->resolve($contextData->getName(), $resourceMeta);
        } catch (DefinitionNotFoundException $e) {
            return null;
        }

        $documentDefinition = new DocumentDefinition($contextData->getResourceNormalizerName());

        foreach ($documentDefinitionBuilderStack as $documentDefinitionBuilder) {
            $documentDefinitionBuilder->buildDefinition($documentDefinition, $resourceMeta->getNormalizerOptions());
        }

        return $documentDefinition;
    }
}
