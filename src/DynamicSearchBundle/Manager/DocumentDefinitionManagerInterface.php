<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinition;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionManagerInterface
{
    /**
     * @param ContextDefinitionInterface  $contextDefinition
     * @param ResourceMetaInterface $resourceMeta
     *
     * @return DocumentDefinition|null
     */
    public function generateDocumentDefinition(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta);
}
