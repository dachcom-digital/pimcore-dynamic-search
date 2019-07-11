<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;

interface DocumentDefinitionBuilderRegistryInterface
{
    /**
     * @return DocumentDefinitionBuilderInterface[]
     */
    public function getAllDocumentDefinitionBuilder();
}