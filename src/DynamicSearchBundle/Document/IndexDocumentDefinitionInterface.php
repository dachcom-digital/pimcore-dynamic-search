<?php

namespace DynamicSearchBundle\Document;

interface IndexDocumentDefinitionInterface
{
    /**
     * @param array $definition
     *
     * @return $this
     */
    public function addDocumentDefinition(array $definition);

    /**
     * @return array
     */
    public function getDocumentDefinitions(): array;

    /**
     * @param array $definition
     *
     * @return $this
     */
    public function addFieldDefinition(array $definition);

    /**
     * @return array
     */
    public function getFieldDefinitions(): array;
}