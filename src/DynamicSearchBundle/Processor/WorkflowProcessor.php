<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Service\LongProcessServiceInterface;

class WorkflowProcessor implements WorkflowProcessorInterface
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
     * @var DataManagerInterface
     */
    protected $dataManager;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var LongProcessServiceInterface
     */
    protected $longProcessService;

    /**
     * @param LoggerInterface             $logger
     * @param ConfigurationInterface      $configuration
     * @param DataManagerInterface        $dataManager
     * @param IndexManagerInterface       $indexManager
     * @param LongProcessServiceInterface $longProcessService
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        DataManagerInterface $dataManager,
        IndexManagerInterface $indexManager,
        LongProcessServiceInterface $longProcessService
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;
        $this->indexManager = $indexManager;
        $this->longProcessService = $longProcessService;
    }

    /**
     * {@inheritDoc}
     */
    public function performFullContextLoop()
    {
        $contextDefinitions = $this->configuration->getContextDefinitions();

        if (count($contextDefinitions) === 0) {
            throw new ProviderException(sprintf('No context configuration found. Please add them to the dynamic_search.context configuration node'));
        }

        $this->longProcessService->boot();

        foreach ($contextDefinitions as $contextDefinition) {

            $dataProvider = $this->dataManager->getDataManger($contextDefinition);
            $indexProvider = $this->indexManager->getIndexManger($contextDefinition);

            $this->warmUpProvider($contextDefinition, $dataProvider);
            $this->warmUpProvider($contextDefinition, $indexProvider);

            $this->executeProvider($contextDefinition, $dataProvider);

            $this->coolDownProvider($contextDefinition, $dataProvider);
            $this->coolDownProvider($contextDefinition, $indexProvider);
        }

        $this->longProcessService->shutdown();

    }

    /**
     * {@inheritDoc}
     */
    public function performSingleContextLoop(string $contextName)
    {
        $this->longProcessService->boot();
        $this->longProcessService->shutdown();
    }

    /**
     * @param ContextDataInterface                         $contextData
     * @param IndexProviderInterface|DataProviderInterface $provider
     */
    protected function warmUpProvider(ContextDataInterface $contextData, $provider)
    {
        try {
            $provider->warmUp($contextData);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Error while warming up data provider. Error was: %s', $e->getMessage()), 'workflow', $contextData->getName());
        }
    }

    /**
     * @param ContextDataInterface                         $contextData
     * @param IndexProviderInterface|DataProviderInterface $provider
     */
    protected function coolDownProvider(ContextDataInterface $contextData, $provider)
    {
        try {
            $provider->coolDown($contextData);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Error while warming up data provider. Error was: %s', $e->getMessage()), 'workflow', $contextData->getName());
        }

    }

    /**
     * @param ContextDataInterface                         $contextData
     * @param IndexProviderInterface|DataProviderInterface $provider
     */
    protected function executeProvider(ContextDataInterface $contextData, $provider)
    {
        try {
            $provider->execute($contextData);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Error while warming up data provider. Error was: %s', $e->getMessage()), 'workflow', $contextData->getName());
        }
    }
}
