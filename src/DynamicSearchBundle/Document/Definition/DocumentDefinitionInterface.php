<?php

namespace DynamicSearchBundle\Document\Definition;

interface DocumentDefinitionInterface
{
    public function getDataNormalizerIdentifier(): string;

    public function setCurrentLevel(string|int $currentLevel): void;

    public function setDocumentConfiguration(array $documentConfiguration): void;

    public function getDocumentConfiguration(): array;

    /**
     * @throws \Exception
     */
    public function addOptionFieldDefinition(array $definition): static;

    public function getOptionFieldDefinitions(): array;

    /**
     * @throws \Exception
     */
    public function addSimpleDocumentFieldDefinition(array $definition): static;

    /**
     * @throws \Exception
     */
    public function addPreProcessFieldDefinition(array $definition, \Closure $closure):  static;

    public function getDocumentFieldDefinitions(): array;
}
