<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace DynamicSearchBundle\Generator;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Manager\DocumentDefinitionManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\TransformerManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Resource\Container\IndexFieldContainer;
use DynamicSearchBundle\Resource\Container\OptionFieldContainer;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndexDocumentGenerator implements IndexDocumentGeneratorInterface
{
    public function __construct(
        protected ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        protected TransformerManagerInterface $transformerManager,
        protected IndexManagerInterface $indexManager,
        protected ResourceHarmonizerInterface $resourceHarmonizer,
        protected DocumentDefinitionManagerInterface $documentDefinitionManager
    ) {
    }

    public function generate(
        ContextDefinitionInterface $contextDefinition,
        ResourceMetaInterface $resourceMeta,
        ResourceContainerInterface $resourceContainer,
        array $options = []
    ): IndexDocument {
        $generatorOptions = $this->buildOptions($options);
        $documentDefinition = $this->generateDocumentDefinition($contextDefinition, $resourceMeta, $generatorOptions);
        $indexDocument = $this->generateIndexDocument($resourceMeta, $documentDefinition);

        return $this->populateIndexDocument($contextDefinition, $indexDocument, $resourceContainer, $documentDefinition);
    }

    public function generateWithoutData(
        ContextDefinitionInterface $contextDefinition,
        array $options = []
    ): IndexDocument {
        $generatorOptions = $this->buildOptions($options);
        $documentDefinition = $this->generateDocumentDefinition($contextDefinition, null, $generatorOptions);
        $indexDocument = $this->generateIndexDocument(null, $documentDefinition);

        foreach ($documentDefinition->getDocumentFieldDefinitions() as $fieldDefinitionOptions) {
            $fieldType = $fieldDefinitionOptions['_field_type'];
            if ($fieldType !== 'simple_definition') {
                continue;
            }

            $this->processDocumentIndexTransformerField($contextDefinition, $indexDocument, $fieldDefinitionOptions, null);
        }

        return $indexDocument;
    }

    protected function buildOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['preConfiguredIndexProvider']);
        $resolver->setAllowedTypes('preConfiguredIndexProvider', ['bool']);
        $resolver->setDefaults([
            'preConfiguredIndexProvider' => false,
        ]);

        return $resolver->resolve($options);
    }

    /**
     * @throws \Exception
     */
    protected function populateIndexDocument(
        ContextDefinitionInterface $contextDefinition,
        IndexDocument $indexDocument,
        ResourceContainerInterface $resourceContainer,
        DocumentDefinitionInterface $documentDefinition
    ): IndexDocument {
        $resourceScaffolderName = $resourceContainer->getResourceScaffolderIdentifier();

        foreach ($documentDefinition->getOptionFieldDefinitions() as $documentDefinitionOptions) {
            $fieldName = $documentDefinitionOptions['name'];
            $dataTransformerOptions = $documentDefinitionOptions['data_transformer'];
            $transformedData = $this->dispatchResourceFieldTransformer($dataTransformerOptions, $resourceScaffolderName, $resourceContainer);

            $optionFieldContainer = new OptionFieldContainer($fieldName, $transformedData);
            $indexDocument->addOptionField($optionFieldContainer);
        }

        foreach ($documentDefinition->getDocumentFieldDefinitions() as $fieldDefinitionOptions) {
            $this->processDocumentDataTransformerField(
                $contextDefinition,
                $indexDocument,
                $resourceContainer,
                $documentDefinition,
                $fieldDefinitionOptions,
                $resourceScaffolderName
            );
        }

        return $indexDocument;
    }

    /**
     * @throws \Exception
     */
    protected function processDocumentDataTransformerField(
        ContextDefinitionInterface $contextDefinition,
        IndexDocument $indexDocument,
        ResourceContainerInterface $resourceContainer,
        DocumentDefinitionInterface $documentDefinition,
        array $fieldDefinitionOptions,
        string $resourceScaffolderName
    ): void {
        $fieldType = $fieldDefinitionOptions['_field_type'];
        $dataTransformerOptions = $fieldDefinitionOptions['data_transformer'];

        $transformedData = $this->dispatchResourceFieldTransformer($dataTransformerOptions, $resourceScaffolderName, $resourceContainer);

        if ($fieldType === 'pre_process_definition') {
            $documentDefinition->setCurrentLevel($fieldDefinitionOptions['level']);

            call_user_func($fieldDefinitionOptions['closure'], $documentDefinition, $transformedData);

            foreach ($documentDefinition->getDocumentFieldDefinitions() as $fieldDefinitionOptions) {
                $this->processDocumentDataTransformerField(
                    $contextDefinition,
                    $indexDocument,
                    $resourceContainer,
                    $documentDefinition,
                    $fieldDefinitionOptions,
                    $resourceScaffolderName
                );
            }
        } elseif ($fieldType === 'simple_definition') {
            $this->processDocumentIndexTransformerField($contextDefinition, $indexDocument, $fieldDefinitionOptions, $transformedData);
        } else {
            throw new \Exception(sprintf('Index field type %s not found.', $fieldType));
        }
    }

    protected function processDocumentIndexTransformerField(
        ContextDefinitionInterface $contextDefinition,
        IndexDocument $indexDocument,
        array $fieldDefinitionOptions,
        mixed $transformedData
    ): void {
        $fieldName = $fieldDefinitionOptions['name'];
        $indexTransformerOptions = $fieldDefinitionOptions['index_transformer'];

        $transformedIndexData = $this->dispatchIndexTransformer($contextDefinition, $fieldName, $indexTransformerOptions, $transformedData);
        if ($transformedIndexData === null) {
            // no error?
            return;
        }

        $indexFieldContainer = new IndexFieldContainer($fieldName, $indexTransformerOptions['type'], $transformedIndexData);
        $indexDocument->addIndexField($indexFieldContainer);
    }

    /**
     * @throws \Exception
     */
    protected function dispatchResourceFieldTransformer(?array $options, string $dispatchTransformerName, ResourceContainerInterface $resourceContainer): mixed
    {
        $fieldTransformerName = $options['type'];

        // it's not a source field
        if ($fieldTransformerName === null) {
            return null;
        }

        $fieldTransformerConfiguration = $options['configuration'];

        $fieldTransformer = $this->transformerManager->getResourceFieldTransformer($dispatchTransformerName, $fieldTransformerName, $fieldTransformerConfiguration);

        if (!$fieldTransformer instanceof FieldTransformerInterface) {
            return null;
        }

        try {
            $transformedData = $fieldTransformer->transformData($dispatchTransformerName, $resourceContainer);
        } catch (\Throwable $e) {
            throw new \Exception(
                sprintf('Error while transform field resource with service "%s": %s', $fieldTransformerName, $e->getMessage())
            );
        }

        if ($transformedData === '') {
            return null;
        }

        return $transformedData;
    }

    /**
     * @throws \Exception
     */
    protected function dispatchIndexTransformer(ContextDefinitionInterface $contextDefinition, string $indexFieldName, array $options, mixed $transformedData): mixed
    {
        $indexTypeName = $options['type'];
        $indexTypeConfiguration = $options['configuration'];

        $indexFieldBuilder = $this->indexManager->getIndexField($contextDefinition, $indexTypeName);
        if (!$indexFieldBuilder instanceof IndexFieldInterface) {
            return null;
        }

        try {
            $indexFieldData = $indexFieldBuilder->build($indexFieldName, $transformedData, $indexTypeConfiguration);
        } catch (\Throwable $e) {
            throw new \Exception(
                sprintf('Error while transform field index with service "%s": %s', $indexTypeName, $e->getMessage())
            );
        }

        return $indexFieldData;
    }

    /**
     * @throws \Exception
     * @throws SilentException
     */
    protected function generateDocumentDefinition(
        ContextDefinitionInterface $contextDefinition,
        ?ResourceMetaInterface $resourceMeta,
        array $generatorOptions
    ): DocumentDefinitionInterface {
        $options = [
            'allowPreProcessFieldDefinitions' => $generatorOptions['preConfiguredIndexProvider'] === false
        ];

        if ($resourceMeta === null) {
            $documentDefinition = $this->documentDefinitionManager->generateDocumentDefinitionForContext($contextDefinition, $options);
        } else {
            $documentDefinition = $this->documentDefinitionManager->generateDocumentDefinition($contextDefinition, $resourceMeta, $options);
        }

        if (!$documentDefinition instanceof DocumentDefinitionInterface) {
            throw new \Exception('No document definition generated. Probably no applicable document definition builder was found. Skipping...');
        }

        if (count($documentDefinition->getDocumentFieldDefinitions()) === 0) {
            throw new SilentException('Document Definition does not have any defined field. Skipping...');
        }

        return $documentDefinition;
    }

    protected function generateIndexDocument(?ResourceMetaInterface $resourceMeta, DocumentDefinitionInterface $documentDefinition): IndexDocument
    {
        return new IndexDocument($resourceMeta, $documentDefinition->getDocumentConfiguration());
    }
}
