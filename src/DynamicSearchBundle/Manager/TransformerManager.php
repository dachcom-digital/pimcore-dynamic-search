<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\DocumentTransformerNotFoundException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Registry\TransformerRegistryInterface;
use DynamicSearchBundle\Resolver\DocumentTransformerResolverInterface;
use DynamicSearchBundle\Transformer\DocumentTransformerContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @var DocumentTransformerResolverInterface
     */
    protected $documentTransformerResolver;

    /**
     * @var TransformerRegistryInterface
     */
    protected $transformerRegistry;

    /**
     * @param LoggerInterface                      $logger
     * @param ConfigurationInterface               $configuration
     * @param DocumentTransformerResolverInterface $documentTransformerResolver
     * @param TransformerRegistryInterface         $transformerRegistry
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        DocumentTransformerResolverInterface $documentTransformerResolver,
        TransformerRegistryInterface $transformerRegistry
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->documentTransformerResolver = $documentTransformerResolver;
        $this->transformerRegistry = $transformerRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentTransformer(ContextDataInterface $contextData, $resource)
    {
        $documentTransformerContainer = null;
        $dataProviderName = $contextData->getDataProviderName();

        try {
            $documentTransformerContainer = $this->documentTransformerResolver->resolve($resource);
        } catch (DocumentTransformerNotFoundException $e) {
            // fail silently
        }

        if (!$documentTransformerContainer instanceof DocumentTransformerContainerInterface) {
            $this->logger->error('No DispatchTransformer found for new data', $dataProviderName, $contextData->getName());
            return null;
        }

        $documentTransformerContainer->getTransformer()->setLogger($this->logger);

        return $documentTransformerContainer;
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldTransformer(string $dispatchTransformerName, string $fieldTransformerName, array $transformerOptions = [])
    {
        if (!$this->transformerRegistry->hasFieldTransformer($dispatchTransformerName, $fieldTransformerName)) {
            return null;
        }

        $fieldTransformer = $this->transformerRegistry->getFieldTransformer($dispatchTransformerName, $fieldTransformerName);

        $optionsResolver = new OptionsResolver();
        $requiredOptionsResolver = $fieldTransformer->configureOptions($optionsResolver);
        $options = $requiredOptionsResolver === false ? [] : $optionsResolver->resolve($transformerOptions);

        $fieldTransformer->setOptions($options);

        return $fieldTransformer;

    }
}
