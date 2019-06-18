<?php

namespace DynamicSearchBundle\Transformer;

interface DispatchTransformerContainerInterface
{
    /**
     * @return DispatchTransformerInterface
     */
    public function getTransformer();

    /**
     * @return string
     */
    public function getIdentifier();
}
