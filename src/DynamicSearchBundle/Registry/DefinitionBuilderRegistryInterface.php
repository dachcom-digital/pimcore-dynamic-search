<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;

interface DefinitionBuilderRegistryInterface
{
    /**
     * @return DocumentDefinitionBuilderInterface[]
     */
    public function getAllDocumentDefinitionBuilder(): array;

    /**
     * @return FilterDefinitionBuilderInterface[]
     */
    public function getAllFilterDefinitionBuilder(): array;
}
