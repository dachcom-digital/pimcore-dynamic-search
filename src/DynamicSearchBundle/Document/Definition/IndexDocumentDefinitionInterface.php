<?php

namespace DynamicSearchBundle\Document\Definition;

interface IndexDocumentDefinitionInterface
{
    /**
     * @param array $documentConfiguration
     */
    public function setDocumentConfiguration(array $documentConfiguration);

    /**
     * @return array
     */
    public function getDocumentConfiguration();

    /**
     * @param array $definition
     *
     * @return $this
     */
    public function addOptionFieldDefinition(array $definition);

    /**
     * @return array
     */
    public function getOptionFieldDefinitions(): array;

    /**
     * @param array $definition
     *
     * @return $this
     */
    public function addIndexFieldDefinition(array $definition);

    /**
     * @return array
     */
    public function getIndexFieldDefinitions(): array;
}