<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
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
     * @var bool
     */
    protected $validProcessRunning;

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
        $this->validProcessRunning = true;

        $contextDefinitions = $this->configuration->getContextDefinitions();

        if (count($contextDefinitions) === 0) {
            throw new RuntimeException('No context configuration found. Please add them to the "dynamic_search.context" configuration node');
        }

        $this->longProcessService->boot();

        foreach ($contextDefinitions as $contextDefinition) {
            $this->dispatchContext($contextDefinition);
        }

        $this->longProcessService->shutdown();

    }

    /**
     * {@inheritDoc}
     */
    public function performSingleContextLoop(string $contextName)
    {
        $this->validProcessRunning = true;

        $contextDefinition = $this->configuration->getContextDefinition($contextName);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        $this->longProcessService->boot();

        $this->dispatchContext($contextDefinition);

        $this->longProcessService->shutdown();

    }

    /**
     * @param ContextDataInterface $contextDefinition
     */
    protected function dispatchContext(ContextDataInterface $contextDefinition)
    {
        $dataProvider = $this->dataManager->getDataProvider($contextDefinition);
        $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);

        $this->warmUpProvider($contextDefinition, [$dataProvider, $indexProvider]);

        $this->executeDataProvider($contextDefinition, $dataProvider, [$dataProvider, $indexProvider]);

        $this->coolDownProvider($contextDefinition, [$dataProvider, $indexProvider]);
    }

    /**
     * @param ContextDataInterface $contextData
     * @param array                $providers
     */
    protected function warmUpProvider(ContextDataInterface $contextData, array $providers)
    {
        if ($this->validProcessRunning === false) {
            return;
        }

        foreach ($providers as $provider) {

            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProvider() : $contextData->getIndexProvider();
            $this->logger->log('DEBUG', sprintf('warm up provider'), $providerName, $contextData->getName());

            try {
                $provider->warmUp($contextData);
            } catch (ProcessCancelledException $e) {
                $this->validProcessRunning = false;
                $errorMessage = sprintf('Process has been cancelled. Message was: %s. Process canceling has been initiated', $e->getMessage());
                $this->dispatchCancelledProcessToProviders($errorMessage, $contextData, $providers);
            } catch (\Throwable $e) {
                $this->validProcessRunning = false;
                $errorMessage = sprintf('Error while warming up provider. Error was: %s. FailOver has been initiated', $e->getMessage());
                $this->dispatchFailOverToProviders($errorMessage, $contextData, $providers);
            }
        }
    }

    /**
     * @param ContextDataInterface $contextData
     * @param array                $providers
     */
    protected function coolDownProvider(ContextDataInterface $contextData, array $providers)
    {
        if ($this->validProcessRunning === false) {
            return;
        }

        foreach ($providers as $provider) {

            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProvider() : $contextData->getIndexProvider();
            $this->logger->log('DEBUG', sprintf('cooling down provider'), $providerName, $contextData->getName());

            try {
                $provider->coolDown($contextData);
            } catch (ProcessCancelledException $e) {
                $this->validProcessRunning = false;
                $errorMessage = sprintf('Process has been cancelled. Message was: %s. Process canceling has been initiated', $e->getMessage());
                $this->dispatchCancelledProcessToProviders($errorMessage, $contextData, $providers);
            } catch (\Throwable $e) {
                $this->validProcessRunning = false;
                $errorMessage = sprintf('Error while cooling down provider. Error was: %s. FailOver has been initiated', $e->getMessage());
                $this->dispatchFailOverToProviders($errorMessage, $contextData, $providers);
            }
        }
    }

    /**
     * @param ContextDataInterface  $contextData
     * @param DataProviderInterface $dataProvider
     * @param array                 $involvedProviders
     */
    protected function executeDataProvider(ContextDataInterface $contextData, $dataProvider, array $involvedProviders)
    {
        if ($this->validProcessRunning === false) {
            return;
        }

        $this->logger->log('DEBUG', sprintf('execute provider'), $contextData->getDataProvider(), $contextData->getName());

        try {
            $dataProvider->execute($contextData);
        } catch (ProcessCancelledException $e) {
            $this->validProcessRunning = false;
            $errorMessage = sprintf('Process has been cancelled. Message was: %s. Process canceling has been initiated', $e->getMessage());
            $this->dispatchCancelledProcessToProviders($errorMessage, $contextData, $involvedProviders);
        } catch (\Throwable $e) {
            $this->validProcessRunning = false;
            $errorMessage = sprintf('Error while executing data provider. Error was: %s. FailOver has been initiated', $e->getMessage());
            $this->dispatchFailOverToProviders($errorMessage, $contextData, $involvedProviders);
        }
    }

    /**
     * @param string               $errorMessage
     * @param ContextDataInterface $contextData
     * @param array                $providers
     */
    protected function dispatchCancelledProcessToProviders(string $errorMessage, ContextDataInterface $contextData, array $providers)
    {
        $this->logger->error($errorMessage, 'workflow', $contextData->getName());

        foreach ($providers as $provider) {
            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProvider() : $contextData->getIndexProvider();
            $this->logger->log('DEBUG', sprintf('executing provider cancelled shutdown'), $providerName, $contextData->getName());

            try {
                $provider->cancelledShutdown($contextData);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf('Error while dispatching cancelled process. Error was: %s.', $e->getMessage()), get_class($provider),
                    $contextData->getName());
            }
        }
    }

    /**
     * @param string               $errorMessage
     * @param ContextDataInterface $contextData
     * @param array                $providers
     */
    protected function dispatchFailOverToProviders(string $errorMessage, ContextDataInterface $contextData, array $providers)
    {
        $this->logger->error($errorMessage, 'workflow', $contextData->getName());

        foreach ($providers as $provider) {
            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProvider() : $contextData->getIndexProvider();
            $this->logger->log('DEBUG', sprintf('executing provider emergency shutdown'), $providerName, $contextData->getName());

            try {
                $provider->emergencyShutdown($contextData);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf('Error while dispatching fail over. Error was: %s.', $e->getMessage()), 'workflow', $contextData->getName());
            }
        }
    }

}
