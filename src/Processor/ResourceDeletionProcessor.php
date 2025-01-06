<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;

class ResourceDeletionProcessor implements ResourceDeletionProcessorInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected IndexManagerInterface $indexManager,
        protected ResourceHarmonizerInterface $resourceHarmonizer
    ) {
    }

    public function process(ContextDefinitionInterface $contextDefinition, $resource): void
    {
        $indexProvider = $this->getIndexProvider($contextDefinition);

        $normalizedResourceStack = $this->resourceHarmonizer->harmonizeUntilNormalizedResourceStack($contextDefinition, $resource);
        if ($normalizedResourceStack === null) {
            // nothing to log: done by harmonizer.
            return;
        }

        foreach ($normalizedResourceStack as $normalizedResource) {
            $resourceMeta = $normalizedResource->getResourceMeta();
            if (empty($resourceMeta->getDocumentId())) {
                $this->logger->error(
                    'Unable to generate index document: No document id given. Skipping...',
                    $contextDefinition->getDataProviderName(),
                    $contextDefinition->getName()
                );

                continue;
            }

            $indexDocument = new IndexDocument($resourceMeta);

            $this->sendIndexDocumentToIndexProvider($contextDefinition, $indexProvider, $indexDocument);
        }
    }

    public function processByResourceMeta(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta): void
    {
        $indexProvider = $this->getIndexProvider($contextDefinition);

        $indexDocument = new IndexDocument($resourceMeta);

        $this->sendIndexDocumentToIndexProvider($contextDefinition, $indexProvider, $indexDocument);
    }

    protected function getIndexProvider(ContextDefinitionInterface $contextDefinition): IndexProviderInterface
    {
        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);
        } catch (\Throwable $e) {
            throw new RuntimeException(
                sprintf(
                    'Unable to load index provider "%s". Error was: %s',
                    $contextDefinition->getIndexProviderName(),
                    $e->getMessage()
                )
            );
        }

        return $indexProvider;
    }

    protected function sendIndexDocumentToIndexProvider(
        ContextDefinitionInterface $contextDefinition,
        IndexProviderInterface $indexProvider,
        IndexDocument $indexDocument
    ): void {
        $this->logger->debug(
            sprintf(
                'Index Document with id "%s" successfully generated. Used "%s" as data normalizer.',
                $indexDocument->getDocumentId(),
                $contextDefinition->getResourceNormalizerName()
            ),
            $contextDefinition->getDataProviderName(),
            $contextDefinition->getName()
        );

        try {
            $indexProvider->processDocument($contextDefinition, $indexDocument);
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf(
                'Error while executing processing index document (%s) via provider. Error was: "%s".',
                $contextDefinition->getContextDispatchType(),
                $e->getMessage()
            ));
        }
    }
}
