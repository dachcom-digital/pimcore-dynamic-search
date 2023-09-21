<?php

namespace DynamicSearchBundle\Document\Definition;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionBuilderInterface
{
    public function isApplicable(string $contextName, ResourceMetaInterface $resourceMeta): bool;

    /**
     * @throws \Exception
     */
    public function buildDefinition(DocumentDefinitionInterface $definition, array $normalizerOptions): DocumentDefinitionInterface;
}
