<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinition;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionManagerInterface
{
    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param array                      $definitionOptions
     *
     * @return DocumentDefinition|null
     *
     * @throws \Exception
     */
    public function generateDocumentDefinitionForContext(ContextDefinitionInterface $contextDefinition, array $definitionOptions = []);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param ResourceMetaInterface      $resourceMeta
     * @param array                      $definitionOptions
     *
     * @return DocumentDefinition|null
     *
     * @throws \Exception
     */
    public function generateDocumentDefinition(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta, array $definitionOptions = []);
}
