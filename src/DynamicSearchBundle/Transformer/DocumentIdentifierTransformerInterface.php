<?php

namespace DynamicSearchBundle\Transformer;

interface DocumentIdentifierTransformerInterface
{
    /**
     * @return DispatchTransformerInterface
     */
    public function find();

    /**
     * @return string
     */
    public function getIdentifier();
}
