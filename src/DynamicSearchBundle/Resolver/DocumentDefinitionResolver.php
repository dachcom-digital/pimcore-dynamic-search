<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\Resolver\DocumentDefinitionNotFoundException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Registry\DocumentDefinitionBuilderRegistryInterface;

class DocumentDefinitionResolver implements DocumentDefinitionResolverInterface
{
    /**
     * @var DocumentDefinitionBuilderRegistryInterface
     */
    protected $documentDefinitionBuilderRegistry;

    /**
     * @param DocumentDefinitionBuilderRegistryInterface $documentDefinitionBuilderRegistry
     */
    public function __construct(DocumentDefinitionBuilderRegistryInterface $documentDefinitionBuilderRegistry)
    {
        $this->documentDefinitionBuilderRegistry = $documentDefinitionBuilderRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $contextName, ResourceMetaInterface $resourceMeta)
    {
        $builder = [];
        foreach ($this->documentDefinitionBuilderRegistry->getAllDocumentDefinitionBuilder() as $documentDefinitionBuilder) {
            if ($documentDefinitionBuilder->isApplicable($contextName, $resourceMeta) === true) {
                $builder[] = $documentDefinitionBuilder;
            }
        }

        if (count($builder) === 0) {
            throw new DocumentDefinitionNotFoundException();
        }

        return $builder;
    }
}
