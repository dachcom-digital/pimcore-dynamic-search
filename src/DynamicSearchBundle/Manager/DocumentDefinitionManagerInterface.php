<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinition;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionManagerInterface
{
    /**
     * @param ContextDataInterface  $contextData
     * @param ResourceMetaInterface $resourceMeta
     *
     * @return DocumentDefinition|null
     */
    public function generateDocumentDefinition(ContextDataInterface $contextData, ResourceMetaInterface $resourceMeta);
}
