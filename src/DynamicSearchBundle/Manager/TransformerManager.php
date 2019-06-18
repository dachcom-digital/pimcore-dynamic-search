<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\DispatchTransformerNotFoundException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Registry\TransformerRegistryInterface;
use DynamicSearchBundle\Resolver\DataResolverInterface;
use DynamicSearchBundle\Transformer\DispatchTransformerContainerInterface;

class TransformerManager implements TransformerManagerInterface
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
     * @var DataResolverInterface
     */
    protected $transformerResolver;

    /**
     * @var TransformerRegistryInterface
     */
    protected $transformerRegistry;

    /**
     * @param LoggerInterface              $logger
     * @param ConfigurationInterface       $configuration
     * @param DataResolverInterface        $transformerResolver
     * @param TransformerRegistryInterface $transformerRegistry
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        DataResolverInterface $transformerResolver,
        TransformerRegistryInterface $transformerRegistry
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->transformerResolver = $transformerResolver;
        $this->transformerRegistry = $transformerRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getDispatchTransformer(ContextDataInterface $contextData, $data)
    {
        $dataTransformerContainer = null;
        $dataProviderName = $contextData->getDataProvider();

        try {
            $dataTransformerContainer = $this->transformerResolver->resolve($data);
        } catch (DispatchTransformerNotFoundException $e) {
            // fail silently
        }

        if (!$dataTransformerContainer instanceof DispatchTransformerContainerInterface) {
            $this->logger->error('No DispatchTransformer found for new data', $dataProviderName, $contextData->getName());
            return null;
        }

        $dataTransformerContainer->getTransformer()->setLogger($this->logger);

        return $dataTransformerContainer;
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldTransformer(string $dispatchTransformerName, string $fieldTransformerName)
    {
        if (!$this->transformerRegistry->hasFieldTransformer($dispatchTransformerName, $fieldTransformerName)) {
            return null;
        }

        return $this->transformerRegistry->getFieldTransformer($dispatchTransformerName, $fieldTransformerName);

    }
}
