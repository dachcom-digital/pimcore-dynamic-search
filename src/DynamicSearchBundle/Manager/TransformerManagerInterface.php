<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderContainerInterface;

interface TransformerManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $resource
     *
     * @returns null|ResourceScaffolderContainerInterface
     */
    public function getResourceScaffolder(ContextDataInterface $contextData, $resource);

    /**
     * @param string $dispatchTransformerName
     * @param string $fieldTransformerName
     * @param array  $transformerOptions
     *
     * @return null|FieldTransformerInterface
     */
    public function getResourceFieldTransformer(string $dispatchTransformerName, string $fieldTransformerName, array $transformerOptions = []);
}
