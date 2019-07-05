<?php

namespace DynamicSearchBundle\Document;

class IndexDocumentDefinition implements IndexDocumentDefinitionInterface
{
    /**
     * @var array
     */
    protected $documentDefinitions;

    /**
     * @var array
     */
    protected $fieldDefinitions;

    /**
     * {@inheritDoc}
     */
    public function addDocumentDefinition(array $definition)
    {
        $this->documentDefinitions[] = $definition;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentDefinitions(): array
    {
        return !is_array($this->documentDefinitions) ? [] : $this->documentDefinitions;
    }

    /**
     * {@inheritDoc}
     */
    public function addFieldDefinition(array $definition)
    {
        $this->fieldDefinitions[] = $definition;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldDefinitions(): array
    {
        return !is_array($this->fieldDefinitions) ? [] : $this->fieldDefinitions;
    }

}