<?php

namespace DynamicSearchBundle\Processor\SubProcessor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinition;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\DocumentDefinitionManagerInterface;
use DynamicSearchBundle\Manager\NormalizerManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;

class IndexDeletionSubProcessor implements IndexDeletionSubProcessorInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var NormalizerManagerInterface
     */
    protected $normalizerManager;

    /**
     * @var DocumentDefinitionManagerInterface
     */
    protected $documentDefinitionManager;

    /**
     * @param LoggerInterface                    $logger
     * @param ConfigurationInterface             $configuration
     * @param IndexManagerInterface              $indexManager
     * @param NormalizerManagerInterface         $normalizerManager
     * @param DocumentDefinitionManagerInterface $documentDefinitionManager
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        IndexManagerInterface $indexManager,
        NormalizerManagerInterface $normalizerManager,
        DocumentDefinitionManagerInterface $documentDefinitionManager
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->indexManager = $indexManager;
        $this->normalizerManager = $normalizerManager;
        $this->documentDefinitionManager = $documentDefinitionManager;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(ContextDataInterface $contextData, ResourceMetaInterface $resourceMeta)
    {
        $documentDefinitionBuilder = $this->documentDefinitionManager->getDocumentDefinitionBuilder($contextData);
        if (!$documentDefinitionBuilder instanceof DocumentDefinitionBuilderInterface) {
            $this->logger->error(sprintf(
                'No index document definition builder for identifier "%s" found. Skipping...',
                $contextData->getDocumentDefinitionBuilderName()
            ),
                $contextData->getIndexProviderName(), $contextData->getName()
            );
            return;
        }

        $resourceNormalizer = $this->normalizerManager->getResourceNormalizer($contextData);
        if (!$resourceNormalizer instanceof ResourceNormalizerInterface) {
            $this->logger->error(
                'No resource normalizer found. Skipping...',
                $contextData->getIndexProviderName(), $contextData->getName()
            );
            return;
        }

        try {
            $definition = new DocumentDefinition($resourceMeta);
            $documentDefinition = $documentDefinitionBuilder->buildDefinition($definition);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf(
                'Error while building index document definition with "%s". Error was: %s. Skipping...',
                $contextData->getDocumentDefinitionBuilderName(),
                $e->getMessage()
            ),
                $contextData->getIndexProviderName(), $contextData->getName()
            );

            return;
        }

        $indexDocument = new IndexDocument($resourceMeta, $documentDefinition->getDocumentConfiguration(), '');

        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextData);
        } catch (\Throwable $e) {
            $this->logger->error(
                sprintf('Unable to load index provider "%s".', $contextData->getIndexProviderName()),
                $contextData->getIndexProviderName(), $contextData->getName()
            );

            return;
        }

        try {
            $contextData->updateRuntimeValue('index_document', $indexDocument);
            $indexProvider->execute($contextData);
        } catch (\Throwable $e) {
            $this->logger->error(
                sprintf('Error while executing index deletion. Error was: "%s".', $e->getMessage()),
                $contextData->getIndexProviderName(), $contextData->getName()
            );
        }
    }
}
