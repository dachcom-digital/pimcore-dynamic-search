<?php

namespace DynamicSearchBundle\Document\Definition;

interface DocumentDefinitionInterface
{
    /**
     * @return string
     */
    public function getDataNormalizerIdentifier();

    /**
     * @param string|int $currentLevel
     */
    public function setCurrentLevel($currentLevel);

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
    public function addSimpleDocumentFieldDefinition(array $definition);

    /**
     * @param array    $preProcessTransformer
     * @param \Closure $closure
     *
     * @return $this
     */
    public function addPreProcessFieldDefinition(array $preProcessTransformer, \Closure $closure);

    /**
     * @return array
     */
    public function getDocumentFieldDefinitions(): array;
}
