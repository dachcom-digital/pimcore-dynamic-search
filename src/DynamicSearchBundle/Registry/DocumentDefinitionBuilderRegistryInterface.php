<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;

interface DocumentDefinitionBuilderRegistryInterface
{
    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has(string $identifier);

    /**
     * @param string $identifier
     *
     * @return DocumentDefinitionBuilderInterface
     */
    public function get(string $identifier);
}