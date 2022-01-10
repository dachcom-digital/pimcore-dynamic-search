<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;

interface DefinitionBuilderRegistryInterface
{
    /**
     * @return array<int, DocumentDefinitionBuilderInterface>
     */
    public function getAllDocumentDefinitionBuilder(): array;

    /**
     * @return array<int, FilterDefinitionBuilderInterface>
     */
    public function getAllFilterDefinitionBuilder(): array;
}
