<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinition;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionManagerInterface
{
    /**
     * @throws \Exception
     */
    public function generateDocumentDefinitionForContext(
        ContextDefinitionInterface $contextDefinition,
        array $definitionOptions = []
    ): ?DocumentDefinition;

    /**
     * @throws \Exception
     */
    public function generateDocumentDefinition(
        ContextDefinitionInterface $contextDefinition,
        ResourceMetaInterface $resourceMeta,
        array $definitionOptions = []
    ): ?DocumentDefinition;
}
