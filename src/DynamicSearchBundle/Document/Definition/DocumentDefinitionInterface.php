<?php

namespace DynamicSearchBundle\Document\Definition;

interface DocumentDefinitionInterface
{
    /**
     * @return string
     */
    public function getDataNormalizerIdentifier();

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
    public function addDocumentFieldDefinition(array $definition);

    /**
     * @return array
     */
    public function getDocumentFieldDefinitions(): array;
}
