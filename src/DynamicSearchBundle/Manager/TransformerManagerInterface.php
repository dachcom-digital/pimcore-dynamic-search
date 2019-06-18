<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Transformer\DispatchTransformerContainerInterface;
use DynamicSearchBundle\Transformer\FieldTransformerInterface;

interface TransformerManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $data
     *
     * @returns null|DispatchTransformerContainerInterface
     */
    public function getDispatchTransformer(ContextDataInterface $contextData, $data);

    /**
     * @param string $dispatchTransformerName
     * @param string $fieldTransformerName
     *
     * @return null|FieldTransformerInterface
     */
    public function getFieldTransformer(string $dispatchTransformerName, string $fieldTransformerName);
}
