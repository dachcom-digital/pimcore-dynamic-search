<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Transformer\DocumentTransformerInterface;
use DynamicSearchBundle\Transformer\FieldTransformerInterface;

interface TransformerRegistryInterface
{
    /**
     * @param string $dispatchTransformerName
     * @param string $identifier
     *
     * @return bool
     */
    public function hasFieldTransformer(string $dispatchTransformerName, string $identifier);

    /**
     * @param string $dispatchTransformerName
     * @param string $identifier
     *
     * @return FieldTransformerInterface
     */
    public function getFieldTransformer(string $dispatchTransformerName, string $identifier);


    /**
     * @return array|DocumentTransformerInterface[]
     */
    public function getAllDispatchTransformers();
}