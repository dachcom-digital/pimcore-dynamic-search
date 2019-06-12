<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Exception\DataTransformerException;
use DynamicSearchBundle\Exception\DataTransformerNotFoundException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Resolver\DataResolverInterface;
use DynamicSearchBundle\Transformer\DataTransformerInterface;

class DataTransformerManager implements DataTransformerManagerInterface
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
    protected $dataTransformerResolver;

    /**
     * @param LoggerInterface        $logger
     * @param ConfigurationInterface $configuration
     * @param DataResolverInterface  $dataTransformerResolver
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        DataResolverInterface $dataTransformerResolver
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->dataTransformerResolver = $dataTransformerResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(string $dataProvider, ContextDataInterface $contextData, $data)
    {
        $indexDocument = null;
        $dataTransformer = null;

        try {
            $dataTransformer = $this->dataTransformerResolver->resolve($data);
        } catch (DataTransformerNotFoundException $e) {
            // fail silently
        }

        if (!$dataTransformer instanceof DataTransformerInterface) {
            $this->logger->error('no transformer found for new data', $dataProvider, $contextData->getName());
            return false;
        }

        $this->applyDataTransformerOptions($dataTransformer, $contextData);

        $dataTransformer->setLogger($this->logger);

        try {
            $indexDocument = $dataTransformer->transformData($contextData, $data);
        } catch (\Throwable $e) {
            throw new DataTransformerException(sprintf('Error while apply data transformation for "%s". Error was: %s', $dataTransformer->getAlias(), $e->getMessage()));
        }

        return $indexDocument;
    }

    /**
     * @param DataTransformerInterface $dataTransformer
     * @param ContextDataInterface     $contextData
     *
     * @throws DataTransformerException
     */
    protected function applyDataTransformerOptions(DataTransformerInterface $dataTransformer, ContextDataInterface $contextData)
    {
        try {
            $contextData->assertValidContextTransformerOptions($dataTransformer, $dataTransformer->getAlias());
        } catch (ContextConfigurationException $e) {
            throw new DataTransformerException(sprintf(
                    'Invalid context configuration for data transformer "%s". Error was: %s',
                    $dataTransformer->getAlias(),
                    $e->getMessage())
            );
        }
    }
}
