<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\DocumentDefinitionManagerInterface;
use DynamicSearchBundle\Manager\NormalizerManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;

class ResourceDeletionProcessor implements ResourceDeletionProcessorInterface
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
     * @var ResourceHarmonizerInterface
     */
    protected $normalizerManager;

    /**
     * @var NormalizerManagerInterface
     */
    protected $resourceHarmonizer;

    /**
     * @var DocumentDefinitionManagerInterface
     */
    protected $documentDefinitionManager;

    /**
     * @param LoggerInterface                    $logger
     * @param ConfigurationInterface             $configuration
     * @param IndexManagerInterface              $indexManager
     * @param NormalizerManagerInterface         $normalizerManager
     * @param ResourceHarmonizerInterface        $resourceHarmonizer
     * @param DocumentDefinitionManagerInterface $documentDefinitionManager
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        IndexManagerInterface $indexManager,
        NormalizerManagerInterface $normalizerManager,
        ResourceHarmonizerInterface $resourceHarmonizer,
        DocumentDefinitionManagerInterface $documentDefinitionManager
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->indexManager = $indexManager;
        $this->normalizerManager = $normalizerManager;
        $this->resourceHarmonizer = $resourceHarmonizer;
        $this->documentDefinitionManager = $documentDefinitionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextDataInterface $contextData, $resource)
    {
        $indexProvider = $this->getIndexProvider($contextData);

        $normalizedResourceStack = $this->resourceHarmonizer->harmonizeUntilNormalizedResourceStack($contextData, $resource);
        if ($normalizedResourceStack === null) {
            // nothing to log: done by harmonizer.
            return;
        }

        foreach ($normalizedResourceStack as $normalizedResource) {
            if (!$normalizedResource instanceof NormalizedDataResourceInterface) {
                $this->logger->error(
                    sprintf('Normalized resource needs to be instance of %s. Skipping...', NormalizedDataResourceInterface::class),
                    $contextData->getIndexProviderName(),
                    $contextData->getName()
                );

                continue;
            }

            $resourceMeta = $normalizedResource->getResourceMeta();
            if (empty($resourceMeta->getDocumentId())) {
                $this->logger->error(
                    'Unable to generate index document: No document id given. Skipping...',
                    $contextData->getIndexProviderName(),
                    $contextData->getName()
                );

                continue;
            }

            $indexDocument = new IndexDocument($resourceMeta);

            $this->sendIndexDocumentToIndexProvider($contextData, $indexProvider, $indexDocument);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processByResourceMeta(ContextDataInterface $contextData, ResourceMetaInterface $resourceMeta)
    {
        $indexProvider = $this->getIndexProvider($contextData);

        $indexDocument = new IndexDocument($resourceMeta);

        $this->sendIndexDocumentToIndexProvider($contextData, $indexProvider, $indexDocument);
    }

    /**
     * @param ContextDataInterface $contextData
     *
     * @return IndexProviderInterface
     */
    protected function getIndexProvider(ContextDataInterface $contextData)
    {
        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextData);
        } catch (\Throwable $e) {
            throw new RuntimeException(
                sprintf(
                    'Unable to load index provider "%s". Error was: %s',
                    $contextData->getIndexProviderName(),
                    $e->getMessage()
                )
            );
        }

        return $indexProvider;
    }

    /**
     * @param ContextDataInterface   $contextData
     * @param IndexProviderInterface $indexProvider
     * @param IndexDocument          $indexDocument
     */
    protected function sendIndexDocumentToIndexProvider(ContextDataInterface $contextData, IndexProviderInterface $indexProvider, IndexDocument $indexDocument)
    {
        $this->logger->debug(
            sprintf(
                'Index Document with id "%s" successfully generated. Used "%s" as data normalizer.',
                $indexDocument->getDocumentId(),
                $contextData->getResourceNormalizerName()
            ),
            $contextData->getIndexProviderName(),
            $contextData->getName()
        );

        try {
            $indexProvider->processDocument($contextData, $indexDocument);
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Error while executing index modification. Error was: "%s".', $e->getMessage()));
        }
    }
}
