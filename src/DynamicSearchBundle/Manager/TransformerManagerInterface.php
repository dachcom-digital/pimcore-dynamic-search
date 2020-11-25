<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderContainerInterface;

interface TransformerManagerInterface
{
    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param mixed                $resource
     *
     * @returns null|ResourceScaffolderContainerInterface
     */
    public function getResourceScaffolder(ContextDefinitionInterface $contextDefinition, $resource);

    /**
     * @param string $dispatchTransformerName
     * @param string $fieldTransformerName
     * @param array  $transformerOptions
     *
     * @return null|FieldTransformerInterface
     */
    public function getResourceFieldTransformer(string $dispatchTransformerName, string $fieldTransformerName, array $transformerOptions = []);
}
