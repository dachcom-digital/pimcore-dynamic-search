<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinition;
use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Resolver\DocumentDefinitionResolverInterface;

class DocumentDefinitionManager implements DocumentDefinitionManagerInterface
{
    public function __construct(
        protected ConfigurationInterface $configuration,
        protected DocumentDefinitionResolverInterface $documentDefinitionResolver
    ) {
    }

    public function generateDocumentDefinitionForContext(
        ContextDefinitionInterface $contextDefinition,
        array $definitionOptions = []
    ): ?DocumentDefinition
    {
        try {
            $documentDefinitionBuilderStack = $this->documentDefinitionResolver->resolveForContext($contextDefinition->getName());
        } catch (DefinitionNotFoundException $e) {
            return null;
        }

        $documentDefinition = new DocumentDefinition($contextDefinition->getResourceNormalizerName(), $definitionOptions);

        foreach ($documentDefinitionBuilderStack as $documentDefinitionBuilder) {
            $documentDefinitionBuilder->buildDefinition($documentDefinition, []);
        }

        return $documentDefinition;
    }

    public function generateDocumentDefinition(
        ContextDefinitionInterface $contextDefinition,
        ResourceMetaInterface $resourceMeta,
        array $definitionOptions = []
    ): ?DocumentDefinition {

        try {
            $documentDefinitionBuilderStack = $this->documentDefinitionResolver->resolve($contextDefinition->getName(), $resourceMeta);
        } catch (DefinitionNotFoundException $e) {
            return null;
        }

        $documentDefinition = new DocumentDefinition($contextDefinition->getResourceNormalizerName(), $definitionOptions);

        foreach ($documentDefinitionBuilderStack as $documentDefinitionBuilder) {
            $documentDefinitionBuilder->buildDefinition($documentDefinition, $resourceMeta->getNormalizerOptions());
        }

        return $documentDefinition;
    }
}
