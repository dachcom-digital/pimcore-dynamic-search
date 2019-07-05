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
use DynamicSearchBundle\Processor\SubProcessor\IndexDeletionSubProcessorInterface;
use DynamicSearchBundle\Processor\SubProcessor\IndexModificationSubProcessorInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Provider\ProviderInterface;
use DynamicSearchBundle\Service\LongProcessServiceInterface;

class ContextProcessor implements ContextProcessorInterface
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
     * @var IndexModificationSubProcessorInterface
     */
    protected $indexModificationSubProcessor;

    /**
     * @var IndexDeletionSubProcessorInterface
     */
    protected $indexDeletionSubProcessor;

    /**
     * @var LongProcessServiceInterface
     */
    protected $longProcessService;

    /**
     * @var bool
     */
    protected $validProcessRunning;

    /**
     * @param LoggerInterface                        $logger
     * @param ConfigurationInterface                 $configuration
     * @param DataManagerInterface                   $dataManager
     * @param IndexManagerInterface                  $indexManager
     * @param QueueManagerInterface                  $queueManager
     * @param IndexModificationSubProcessorInterface $indexModificationSubProcessor
     * @param IndexDeletionSubProcessorInterface     $indexDeletionSubProcessor
     * @param LongProcessServiceInterface            $longProcessService
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        DataManagerInterface $dataManager,
        IndexManagerInterface $indexManager,
        QueueManagerInterface $queueManager,
        IndexModificationSubProcessorInterface $indexModificationSubProcessor,
        IndexDeletionSubProcessorInterface $indexDeletionSubProcessor,
        LongProcessServiceInterface $longProcessService
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;
        $this->indexManager = $indexManager;
        $this->queueManager = $queueManager;
        $this->indexModificationSubProcessor = $indexModificationSubProcessor;
        $this->indexDeletionSubProcessor = $indexDeletionSubProcessor;
        $this->longProcessService = $longProcessService;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatchFullContextCreation(array $runtimeValues = [])
    {
        $contextDefinitions = $this->configuration->getContextDefinitions(ContextDataInterface::CONTEXT_DISPATCH_TYPE_INDEX, $runtimeValues);

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
    public function dispatchSingleContextCreation(string $contextName, array $runtimeValues = [])
    {
        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_INDEX, $contextName, $runtimeValues);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        $this->queueManager->clearQueue();

        $this->longProcessService->boot();

        $this->dispatchContext($contextDefinition);

        $this->longProcessService->shutdown();

    }

    /**
     * {@inheritDoc}
     */
    public function dispatchContextModification(string $contextName, string $dispatchType, $resource, array $runtimeValues = [])
    {
        $this->dispatchContextModificationStack($contextName, $dispatchType, [$resource], $runtimeValues);
    }

    /**
     * {@inheritDoc}
     */
    public function dispatchContextModificationStack(string $contextName, string $dispatchType, array $resources, array $runtimeValues = [])
    {
        $contextDefinition = $this->configuration->getContextDefinition($dispatchType, $contextName);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);
        } catch (ProviderException $e) {
            $this->logger->error(sprintf('Error while fetching provider. Error was: %s.', $e->getMessage()), 'workflow', $contextDefinition->getName());
            return;
        }

        $this->warmUpProvider($contextDefinition, [$indexProvider]);

        foreach ($resources as $resource) {
            $this->executeIndexSubProcessor($contextDefinition, $indexProvider, $resource);
        }

        $this->coolDownProvider($contextDefinition, [$indexProvider]);
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
            $this->logger->error(sprintf('Error while fetching provider. Error was: %s.', $e->getMessage()), 'workflow', $contextDefinition->getName());
            return;
        }

        $this->warmUpProvider($contextDefinition, [$dataProvider, $indexProvider]);

        $this->executeProvider($contextDefinition, $dataProvider, [$dataProvider, $indexProvider]);

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
        $this->logger->log('DEBUG', sprintf('execute provider for dispatch type "%s"', $contextData->getContextDispatchType()), $providerName, $contextData->getName());

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
     * @param ContextDataInterface   $contextData
     * @param IndexProviderInterface $indexProvider
     * @param mixed                  $resource
     */
    protected function executeIndexSubProcessor(ContextDataInterface $contextData, IndexProviderInterface $indexProvider, $resource)
    {
        if ($this->validProcessRunning === false) {
            return;
        }

        $this->logger->log('DEBUG', sprintf(
            'execute index context processor for dispatch type "%s"',
            $contextData->getContextDispatchType()),
            $contextData->getIndexProviderName(),
            $contextData->getName()
        );

        try {
            if ($contextData->getContextDispatchType() === ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE) {
                $this->indexDeletionSubProcessor->dispatch($contextData, $resource);
            } else {
                $this->indexModificationSubProcessor->dispatch($contextData, $resource);
            }
        } catch (\Throwable $e) {
            $this->validProcessRunning = false;
            $errorMessage = sprintf(
                'Error while executing index %s processor. Error was: %s. FailOver has been initiated',
                $contextData->getContextDispatchType(), $e->getMessage()
            );
            $this->dispatchFailOverToProviders($errorMessage, $contextData, [$indexProvider]);
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
