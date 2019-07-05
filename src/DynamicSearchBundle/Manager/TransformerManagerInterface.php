<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Transformer\DocumentTransformerInterface;
use DynamicSearchBundle\Transformer\FieldTransformerInterface;

interface TransformerManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $resource
     *
     * @returns null|DocumentTransformerInterface
     */
    public function getDocumentTransformer(ContextDataInterface $contextData, $resource);

    /**
     * @param string $dispatchTransformerName
     * @param string $fieldTransformerName
     * @param array  $transformerOptions
     *
     * @return null|FieldTransformerInterface
     */
    public function getFieldTransformer(string $dispatchTransformerName, string $fieldTransformerName, array $transformerOptions = []);
}
