<?php

namespace DynamicSearchBundle\Document\Definition;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionBuilderInterface
{
    /**
     * @param string                $contextName
     * @param ResourceMetaInterface $resourceMeta
     *
     * @return bool
     */
    public function isApplicable(string $contextName, ResourceMetaInterface $resourceMeta);

    /**
     * @param DocumentDefinitionInterface $definition
     * @param array                       $normalizerOptions
     *
     * @return DocumentDefinitionInterface
     * @throws \Exception
     */
    public function buildDefinition(DocumentDefinitionInterface $definition, array $normalizerOptions);
}
