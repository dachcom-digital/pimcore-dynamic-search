<?php

namespace DynamicSearchBundle\Document\Definition;

interface OutputDocumentDefinitionInterface
{
    /**
     * @param array $definition
     *
     * @return $this
     */
    public function addOutputFieldDefinition(array $definition);

    /**
     * @return array
     */
    public function getOutputFieldDefinitions(): array;
}