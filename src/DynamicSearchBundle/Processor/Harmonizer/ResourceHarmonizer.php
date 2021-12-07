<?php

namespace DynamicSearchBundle\Processor\Harmonizer;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\NormalizerException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\NormalizerManagerInterface;
use DynamicSearchBundle\Manager\TransformerManagerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;
use DynamicSearchBundle\Resource\Container\ResourceContainer;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderContainerInterface;

class ResourceHarmonizer implements ResourceHarmonizerInterface
{
    protected LoggerInterface $logger;
    protected TransformerManagerInterface $transformerManager;
    protected NormalizerManagerInterface $normalizerManager;

    public function __construct(
        LoggerInterface $logger,
        TransformerManagerInterface $transformerManager,
        NormalizerManagerInterface $normalizerManager
    ) {
        $this->logger = $logger;
        $this->transformerManager = $transformerManager;
        $this->normalizerManager = $normalizerManager;
    }

    public function harmonizeUntilNormalizedResourceStack(ContextDefinitionInterface $contextDefinition, $resource): ?array
    {
        $resourceContainer = $this->harmonizeUntilResourceContainer($contextDefinition, $resource);

        if (!$resourceContainer instanceof ResourceContainerInterface) {
            // nothing to log: done by harmonizeToResourceContainer() method.
            return null;
        }

        try {
            $resourceNormalizer = $this->normalizerManager->getResourceNormalizer($contextDefinition);
        } catch (NormalizerException $e) {
            $this->logger->error(
                sprintf(
                    'Unable to load resource normalizer "%s". Error was: %s. Skipping...',
                    $contextDefinition->getResourceNormalizerName(),
                    $e->getMessage()
                ),
                $contextDefinition->getIndexProviderName(),
                $contextDefinition->getName()
            );

            return null;
        }

        if (!$resourceNormalizer instanceof ResourceNormalizerInterface) {
            $this->logger->error(
                sprintf(
                    'No resource normalizer "%s" found. Skipping...',
                    $contextDefinition->getResourceNormalizerName()
                ),
                $contextDefinition->getIndexProviderName(),
                $contextDefinition->getName()
            );

            return null;
        }

        try {
            $normalizedResourceStack = $resourceNormalizer->normalizeToResourceStack($contextDefinition, $resourceContainer);
        } catch (NormalizerException $e) {
            $this->logger->error(
                sprintf(
                    'Error while generating normalized resource stack with identifier "%s". Error was: %s. Skipping...',
                    $contextDefinition->getResourceNormalizerName(),
                    $e->getMessage()
                ),
                $contextDefinition->getIndexProviderName(),
                $contextDefinition->getName()
            );

            return null;
        }

        if (count($normalizedResourceStack) === 0) {
            $this->logger->debug(
                sprintf('No normalized resources generated. Used resource normalizer: %s. Skipping...', $contextDefinition->getResourceNormalizerName()),
                $contextDefinition->getIndexProviderName(),
                $contextDefinition->getName()
            );

            return null;
        }

        return $normalizedResourceStack;
    }

    public function harmonizeUntilResourceContainer(ContextDefinitionInterface $contextDefinition, $resource): ?ResourceContainerInterface
    {
        $resourceScaffolderContainer = $this->transformerManager->getResourceScaffolder($contextDefinition, $resource);
        if (!$resourceScaffolderContainer instanceof ResourceScaffolderContainerInterface) {
            $this->logger->debug(
                'No resource scaffolder has been found. Skipping...',
                $contextDefinition->getIndexProviderName(),
                $contextDefinition->getName()
            );

            return null;
        }

        $scaffoldResourceAttributes = $resourceScaffolderContainer->getScaffolder()->setup($contextDefinition, $resource);
        $isBaseResource = $resourceScaffolderContainer->getScaffolder()->isBaseResource($resource);

        return new ResourceContainer($resource, $isBaseResource, $resourceScaffolderContainer->getIdentifier(), $scaffoldResourceAttributes);
    }
}
