<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionContextBuilderInterface;
use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Registry\DefinitionBuilderRegistryInterface;

class DocumentDefinitionResolver implements DocumentDefinitionResolverInterface
{
    public function __construct(protected DefinitionBuilderRegistryInterface $definitionBuilderRegistry)
    {
    }

    public function resolveForContext(string $contextName): array
    {
        $builder = [];
        foreach ($this->definitionBuilderRegistry->getAllDocumentDefinitionBuilder() as $documentDefinitionBuilder) {

            if (!$documentDefinitionBuilder instanceof DocumentDefinitionContextBuilderInterface) {
                continue;
            }

            if ($documentDefinitionBuilder->isApplicableForContext($contextName) === true) {
                $builder[] = $documentDefinitionBuilder;
            }
        }

        if (count($builder) === 0) {
            throw new DefinitionNotFoundException('document');
        }

        return $builder;
    }

    public function resolve(string $contextName, ResourceMetaInterface $resourceMeta): array
    {
        $builder = [];
        foreach ($this->definitionBuilderRegistry->getAllDocumentDefinitionBuilder() as $documentDefinitionBuilder) {
            if ($documentDefinitionBuilder->isApplicable($contextName, $resourceMeta) === true) {
                $builder[] = $documentDefinitionBuilder;
            }
        }

        if (count($builder) === 0) {
            throw new DefinitionNotFoundException('document');
        }

        return $builder;
    }
}
