<?php

namespace DynamicSearchBundle\Document\Definition;

interface PreProcessedDocumentDefinitionInterface
{
    /**
     * @param array $definition
     *
     * @return $this
     */
    public function addSimpleDocumentFieldDefinition(array $definition);

    /**
     * @return array
     */
    public function getDocumentFieldDefinitions(): array;
}
