<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\TransformerManagerInterface;
use DynamicSearchBundle\Transformer\Container\DataContainerInterface;
use DynamicSearchBundle\Transformer\Container\FieldContainerInterface;
use DynamicSearchBundle\Transformer\DispatchTransformerContainerInterface;
use DynamicSearchBundle\Transformer\FieldTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransformerWorkflowProcessor implements TransformerWorkflowProcessorInterface
{
    /**
     * @var TransformerManagerInterface
     */
    protected $transformerManager;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @param TransformerManagerInterface $transformerManager
     * @param IndexManagerInterface       $indexManager
     */
    public function __construct(
        TransformerManagerInterface $transformerManager,
        IndexManagerInterface $indexManager
    ) {
        $this->transformerManager = $transformerManager;
        $this->indexManager = $indexManager;
    }

    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $data
     *
     * @return IndexDocument|null
     * @throws \Exception
     */
    public function dispatchIndexDocumentTransform(ContextDataInterface $contextData, $data)
    {
        $dispatchTransformerContainer = $this->transformerManager->getDispatchTransformer($contextData, $data);
        if (!$dispatchTransformerContainer instanceof DispatchTransformerContainerInterface) {
            return null;
        }

        $dispatchedTransformerData = $dispatchTransformerContainer->getTransformer()->transformData($contextData, $data);
        if (!$dispatchedTransformerData instanceof DataContainerInterface) {
            return null;
        }

        $indexDocument = $this->generateIndexDocument($contextData, $dispatchTransformerContainer->getIdentifier(), $dispatchedTransformerData);
        $indexDocumentFields = $this->generateIndexDocumentFields($contextData, $dispatchTransformerContainer->getIdentifier(), $dispatchedTransformerData);

        foreach ($indexDocumentFields as $indexDocumentField) {

            $indexFieldBuilder = $this->indexManager->getIndexField($contextData, $indexDocumentField->getIndexType());

            if (!$indexFieldBuilder instanceof IndexFieldInterface) {
                continue;
            }

            $indexField = $indexFieldBuilder->build($indexDocumentField);

            $indexDocument->addField($indexField, $indexDocumentField);
        }

        return $indexDocument;
    }

    /**
     * @param ContextDataInterface   $contextData
     * @param string                 $dispatchTransformerName
     * @param DataContainerInterface $transformedData
     *
     * @return IndexDocument
     * @throws \Exception
     */
    protected function generateIndexDocument(ContextDataInterface $contextData, string $dispatchTransformerName, DataContainerInterface $transformedData)
    {
        $documentOptions = $contextData->getDocumentOptionsConfig();

        $indexDocumentOptions = [];

        foreach ($documentOptions as $documentOptionName => $documentOption) {

            $fieldTransformerName = $documentOption['field_transformer'];
            $fieldTransformerOptions = $documentOption['field_transformer_options'];

            $fieldTransformer = $this->transformerManager->getFieldTransformer($dispatchTransformerName, $fieldTransformerName);
            if (!$fieldTransformer instanceof FieldTransformerInterface) {
                continue;
            }

            $optionsResolver = new OptionsResolver();
            $fieldTransformer->configureOptions($optionsResolver);

            $options = $optionsResolver->resolve($fieldTransformerOptions);

            $indexDocumentOptions[$documentOptionName] = $fieldTransformer->transformData($options, $dispatchTransformerName, $transformedData);

        }

        $indexDocument = new IndexDocument($indexDocumentOptions, $dispatchTransformerName);

        return $indexDocument;
    }

    /**
     * @param ContextDataInterface   $contextData
     * @param string                 $dispatchTransformerName
     * @param DataContainerInterface $transformedData
     *
     * @return array|FieldContainerInterface[]
     */
    protected function generateIndexDocumentFields(ContextDataInterface $contextData, string $dispatchTransformerName, DataContainerInterface $transformedData)
    {
        $documentFields = $contextData->getDocumentFieldsConfig();

        $indexDocumentFields = [];

        foreach ($documentFields as $documentFieldName => $documentFieldOptions) {

            $fieldTransformerName = $documentFieldOptions['field_transformer'];
            $fieldTransformerOptions = $documentFieldOptions['field_transformer_options'];
            $fieldTransformerIndexType = $documentFieldOptions['index_type'];

            $fieldTransformer = $this->transformerManager->getFieldTransformer($dispatchTransformerName, $fieldTransformerName);

            if (!$fieldTransformer instanceof FieldTransformerInterface) {
                continue;
            }

            $optionsResolver = new OptionsResolver();
            $requiredOptionsResolver = $fieldTransformer->configureOptions($optionsResolver);

            if ($requiredOptionsResolver === false) {
                $options = [];
            } else {
                $options = $optionsResolver->resolve($fieldTransformerOptions);
            }

            $fieldTransformerContainer = $fieldTransformer->transformData($options, $dispatchTransformerName, $transformedData);

            if (!$fieldTransformerContainer instanceof FieldContainerInterface) {
                continue;
            }

            $fieldTransformerContainer->setName($documentFieldName);
            $fieldTransformerContainer->setIndexType($fieldTransformerIndexType);

            $indexDocumentFields[] = $fieldTransformerContainer;

        }

        return $indexDocumentFields;

    }
}
