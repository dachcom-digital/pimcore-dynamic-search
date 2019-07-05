<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Document\IndexDocumentDefinitionBuilderInterface;

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
     * @return IndexDocumentDefinitionBuilderInterface
     */
    public function get(string $identifier);
}