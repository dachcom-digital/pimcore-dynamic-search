<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\QueueManagerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\ProviderInterface;
use DynamicSearchBundle\Service\LongProcessServiceInterface;

class ContextWorkflowProcessor implements ContextWorkflowProcessorInterface
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
     * @var QueueManagerInterface
     */
    protected $queueManager;

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
     * @param QueueManagerInterface       $queueManager
     * @param LongProcessServiceInterface $longProcessService
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        DataManagerInterface $dataManager,
        IndexManagerInterface $indexManager,
        QueueManagerInterface $queueManager,
        LongProcessServiceInterface $longProcessService
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;
        $this->indexManager = $indexManager;
        $this->queueManager = $queueManager;
        $this->longProcessService = $longProcessService;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatchFullContextCreation(array $runtimeOptions = [])
    {
        $contextDefinitions = $this->configuration->getContextDefinitions(ContextDataInterface::CONTEXT_DISPATCH_TYPE_INDEX, $runtimeOptions);

        if (count($contextDefinitions) === 0) {
            throw new RuntimeException('No context configuration found. Please add them to the "dynamic_search.context" configuration node');
        }

        $this->queueManager->clearQueue();

        $this->longProcessService->boot();

        foreach ($contextDefinitions as $contextDefinition) {
            $this->dispatchContext($contextDefinition);
        }

        $this->longProcessService->shutdown();

    }

    /**
     * {@inheritDoc}
     */
    public function dispatchSingleContextCreation(string $contextName, array $runtimeOptions = [])
    {
        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_INDEX, $contextName, $runtimeOptions);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        $this->queueManager->clearQueue();

        $this->longProcessService->boot();

        $this->dispatchContext($contextDefinition);

        $this->longProcessService->shutdown();

    }

    /**
     * @param ContextDataInterface $contextDefinition
     */
    protected function dispatchContext(ContextDataInterface $contextDefinition)
    {
        $this->validProcessRunning = true;

        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition);
            $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);
        } catch (ProviderException $e) {
            $this->logger->error(sprintf('Error while dispatching fail over. Error was: %s.', $e->getMessage()), 'workflow', $contextDefinition->getName());
            return;
        }

        $this->warmUpProvider($contextDefinition, [$dataProvider, $indexProvider]);

        $this->executeProvider($contextDefinition, $dataProvider, [$dataProvider, $indexProvider]);

        $this->coolDownProvider($contextDefinition, [$dataProvider, $indexProvider]);
    }

    /**
     * {@inheritDoc}
     */
    public function dispatchInsert(string $contextName, array $runtimeOptions = [])
    {
        $this->validProcessRunning = true;

        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_INSERT, $contextName, $runtimeOptions);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition);
        } catch (ProviderException $e) {
            $this->logger->error(sprintf('Error while getting index provider. Error was: %s.', $e->getMessage()), 'workflow', $contextDefinition->getName());
            return;
        }

        $this->warmUpProvider($contextDefinition, [$dataProvider]);

        $this->executeProvider($contextDefinition, $dataProvider, [$dataProvider]);

        $this->coolDownProvider($contextDefinition, [$dataProvider]);
    }

    /**
     * {@inheritDoc}
     */
    public function dispatchUpdate(string $contextName, array $runtimeOptions = [])
    {
        $this->validProcessRunning = true;

        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_UPDATE, $contextName, $runtimeOptions);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition);
        } catch (ProviderException $e) {
            $this->logger->error(sprintf('Error while getting data provider. Error was: %s.', $e->getMessage()), 'workflow', $contextDefinition->getName());
            return;
        }

        $this->warmUpProvider($contextDefinition, [$dataProvider]);

        $this->executeProvider($contextDefinition, $dataProvider, [$dataProvider]);

        $this->coolDownProvider($contextDefinition, [$dataProvider]);
    }

    /**
     * {@inheritDoc}
     */
    public function dispatchDeletion(string $contextName, array $runtimeOptions = [])
    {
        $this->validProcessRunning = true;

        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE, $contextName, $runtimeOptions);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);
        } catch (ProviderException $e) {
            $this->logger->error(sprintf('Error while getting index provider. Error was: %s.', $e->getMessage()), 'workflow', $contextDefinition->getName());
            return;
        }

        $this->warmUpProvider($contextDefinition, [$indexProvider]);

        $this->executeProvider($contextDefinition, $indexProvider, [$indexProvider]);

        $this->coolDownProvider($contextDefinition, [$indexProvider]);
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

            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProviderName() : $contextData->getIndexProviderName();
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

            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProviderName() : $contextData->getIndexProviderName();
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
     * @param ContextDataInterface $contextData
     * @param ProviderInterface    $provider
     * @param array                $involvedProviders
     */
    protected function executeProvider(ContextDataInterface $contextData, $provider, array $involvedProviders)
    {
        if ($this->validProcessRunning === false) {
            return;
        }

        $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProviderName() : $contextData->getIndexProviderName();
        $this->logger->log('DEBUG', sprintf('execute provider for dispatch type "%s"', $contextData->getDispatchType()), $providerName, $contextData->getName());

        try {
            $provider->execute($contextData);
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
            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProviderName() : $contextData->getIndexProviderName();
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
            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProviderName() : $contextData->getIndexProviderName();
            $this->logger->log('DEBUG', sprintf('executing provider emergency shutdown'), $providerName, $contextData->getName());

            try {
                $provider->emergencyShutdown($contextData);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf('Error while dispatching fail over. Error was: %s.', $e->getMessage()), 'workflow', $contextData->getName());
            }
        }
    }

}
