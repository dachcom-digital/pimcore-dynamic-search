<?php

namespace DynamicSearchBundle\Transformer;

interface DocumentTransformerContainerInterface
{
    /**
     * @return DocumentTransformerInterface
     */
    public function getTransformer();

    /**
     * @return string
     */
    public function getIdentifier();
}
