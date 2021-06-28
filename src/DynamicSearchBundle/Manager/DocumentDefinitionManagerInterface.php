<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinition;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionManagerInterface
{
    public function generateDocumentDefinitionForContext(
        ContextDefinitionInterface $contextDefinition,
        array $definitionOptions = []
    ): ?DocumentDefinitionInterface;

    public function generateDocumentDefinition(
        ContextDefinitionInterface $contextDefinition,
        ResourceMetaInterface $resourceMeta,
        array $definitionOptions = []
    ): ?DocumentDefinitionInterface;
}
