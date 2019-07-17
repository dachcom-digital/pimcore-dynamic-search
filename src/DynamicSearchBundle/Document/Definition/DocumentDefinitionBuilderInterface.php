<?php

namespace DynamicSearchBundle\Document\Definition;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionBuilderInterface
{
    /**
     * @param ResourceMetaInterface $resourceMeta
     *
     * @return bool
     */
    public function isApplicable(ResourceMetaInterface $resourceMeta);

    /**
     * @param DocumentDefinitionInterface $definition
     * @param array                       $normalizerOptions
     *
     * @return DocumentDefinitionInterface
     */
    public function buildDefinition(DocumentDefinitionInterface $definition, array $normalizerOptions);
}
