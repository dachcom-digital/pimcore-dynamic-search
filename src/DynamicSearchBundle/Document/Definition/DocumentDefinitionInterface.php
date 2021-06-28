<?php

namespace DynamicSearchBundle\Document\Definition;

interface DocumentDefinitionInterface
{
    public function getDataNormalizerIdentifier(): string;

    /**
     * @param string|int $currentLevel
     */
    public function setCurrentLevel($currentLevel);

    public function setDocumentConfiguration(array $documentConfiguration): void;

    public function getDocumentConfiguration(): array;

    public function addOptionFieldDefinition(array $definition): static;

    public function getOptionFieldDefinitions(): array;

    public function addSimpleDocumentFieldDefinition(array $definition): static;

    public function addPreProcessFieldDefinition(array $definition, \Closure $closure): static;

    public function getDocumentFieldDefinitions(): array;
}
