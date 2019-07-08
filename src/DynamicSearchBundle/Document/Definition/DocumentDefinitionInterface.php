<?php

namespace DynamicSearchBundle\Document\Definition;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionInterface
{
    /**
     * @return ResourceMetaInterface
     */
    public function getResourceMeta();

    /**
     * @return array
     */
    public function getOptions();

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