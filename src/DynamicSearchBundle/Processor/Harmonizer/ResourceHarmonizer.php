<?php

namespace DynamicSearchBundle\Processor\Harmonizer;

use DynamicSearchBundle\Context\ContextDataInterface;
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
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TransformerManagerInterface
     */
    protected $transformerManager;

    /**
     * @var NormalizerManagerInterface
     */
    protected $normalizerManager;

    /**
     * @param LoggerInterface             $logger
     * @param TransformerManagerInterface $transformerManager
     * @param NormalizerManagerInterface  $normalizerManager
     */
    public function __construct(
        LoggerInterface $logger,
        TransformerManagerInterface $transformerManager,
        NormalizerManagerInterface $normalizerManager
    ) {
        $this->logger = $logger;
        $this->transformerManager = $transformerManager;
        $this->normalizerManager = $normalizerManager;
    }

    /**
     * {@inheritdoc}
     */
    public function harmonizeUntilNormalizedResourceStack(ContextDataInterface $contextData, $resource)
    {
        $resourceContainer = $this->harmonizeUntilResourceContainer($contextData, $resource);

        if (!$resourceContainer instanceof ResourceContainerInterface) {
            // nothing to log: done by harmonizeToResourceContainer() method.
            return null;
        }

        try {
            $resourceNormalizer = $this->normalizerManager->getResourceNormalizer($contextData);
        } catch (NormalizerException $e) {
            $this->logger->error(
                sprintf(
                    'Unable to load resource normalizer "%s". Error was: %s. Skipping...',
                    $contextData->getResourceNormalizerName(),
                    $e->getMessage()
                ),
                $contextData->getIndexProviderName(),
                $contextData->getName()
            );

            return null;
        }

        if (!$resourceNormalizer instanceof ResourceNormalizerInterface) {
            $this->logger->error(
                sprintf(
                    'No resource normalizer "%s" found. Skipping...',
                    $contextData->getResourceNormalizerName()
                ),
                $contextData->getIndexProviderName(),
                $contextData->getName()
            );

            return null;
        }

        try {
            $normalizedResourceStack = $resourceNormalizer->normalizeToResourceStack($contextData, $resourceContainer);
        } catch (NormalizerException $e) {
            $this->logger->error(
                sprintf(
                    'Error while generating normalized resource stack with identifier "%s". Error was: %s. Skipping...',
                    $contextData->getResourceNormalizerName(),
                    $e->getMessage()
                ),
                $contextData->getIndexProviderName(),
                $contextData->getName()
            );

            return null;
        }

        if (count($normalizedResourceStack) === 0) {
            $this->logger->debug(
                sprintf('No normalized resources generated. Used resource normalizer: %s. Skipping...', $contextData->getResourceNormalizerName()),
                $contextData->getIndexProviderName(),
                $contextData->getName()
            );

            return null;
        }

        return $normalizedResourceStack;
    }

    /**
     * {@inheritdoc}
     */
    public function harmonizeUntilResourceContainer(ContextDataInterface $contextData, $resource)
    {
        $resourceScaffolderContainer = $this->transformerManager->getResourceScaffolder($contextData, $resource);
        if (!$resourceScaffolderContainer instanceof ResourceScaffolderContainerInterface) {
            $this->logger->debug(
                'No resource scaffolder has been found. Skipping...',
                $contextData->getIndexProviderName(),
                $contextData->getName()
            );

            return null;
        }

        $scaffoldResourceAttributes = $resourceScaffolderContainer->getScaffolder()->setup($contextData, $resource);
        $isBaseResource = $resourceScaffolderContainer->getScaffolder()->isBaseResource($resource);
        $resourceContainer = new ResourceContainer($resource, $isBaseResource, $resourceScaffolderContainer->getIdentifier(), $scaffoldResourceAttributes);

        return $resourceContainer;
    }
}
