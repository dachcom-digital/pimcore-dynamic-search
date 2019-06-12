<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Transformer\DataTransformerInterface;

interface DataTransformerRegistryInterface
{
    /**
     * @return DataTransformerInterface[]
     */
    public function all();
}