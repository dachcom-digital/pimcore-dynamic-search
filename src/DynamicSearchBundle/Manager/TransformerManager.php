<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\Resolver\ResourceScaffolderNotFoundException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Registry\TransformerRegistryInterface;
use DynamicSearchBundle\Resolver\ResourceScaffolderResolverInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderContainerInterface;
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
     * @var ResourceScaffolderResolverInterface
     */
    protected $documentTransformerResolver;

    /**
     * @var TransformerRegistryInterface
     */
    protected $transformerRegistry;

    /**
     * @param LoggerInterface                     $logger
     * @param ConfigurationInterface              $configuration
     * @param ResourceScaffolderResolverInterface $documentTransformerResolver
     * @param TransformerRegistryInterface        $transformerRegistry
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        ResourceScaffolderResolverInterface $documentTransformerResolver,
        TransformerRegistryInterface $transformerRegistry
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->documentTransformerResolver = $documentTransformerResolver;
        $this->transformerRegistry = $transformerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceScaffolder(ContextDefinitionInterface $contextDefinition, $resource)
    {
        $resourceScaffolderContainer = null;
        $dataProviderName = $contextDefinition->getDataProviderName();

        try {
            $resourceScaffolderContainer = $this->documentTransformerResolver->resolve($contextDefinition->getDataProviderName(), $resource);
        } catch (ResourceScaffolderNotFoundException $e) {
            // fail silently to log incident
        }

        if (!$resourceScaffolderContainer instanceof ResourceScaffolderContainerInterface) {
            $this->logger->error('No Resource Scaffolder found for new data', $dataProviderName, $contextDefinition->getName());

            return null;
        }

        return $resourceScaffolderContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceFieldTransformer(string $dispatchTransformerName, string $fieldTransformerName, array $transformerOptions = [])
    {
        if (!$this->transformerRegistry->hasResourceFieldTransformer($dispatchTransformerName, $fieldTransformerName)) {
            return null;
        }

        $fieldTransformer = $this->transformerRegistry->getResourceFieldTransformer($dispatchTransformerName, $fieldTransformerName);

        $optionsResolver = new OptionsResolver();
        $requiredOptionsResolver = $fieldTransformer->configureOptions($optionsResolver);
        $options = $requiredOptionsResolver === false ? [] : $optionsResolver->resolve($transformerOptions);

        $fieldTransformer->setOptions($options);

        return $fieldTransformer;
    }
}
