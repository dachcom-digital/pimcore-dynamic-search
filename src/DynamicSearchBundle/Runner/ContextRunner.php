<?php

namespace DynamicSearchBundle\Runner;

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
use DynamicSearchBundle\Service\LongProcessServiceInterface;

class ContextRunner extends AbstractRunner implements ContextRunnerInterface
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
    public function runFullContextCreation(array $runtimeValues = [])
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
    public function runSingleContextCreation(string $contextName, array $runtimeValues = [])
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
     * @param ContextDataInterface $contextDefinition
     */
    protected function dispatchContext(ContextDataInterface $contextDefinition)
    {
        $this->validProcessRunning = true;

        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH);
            $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);
        } catch (ProviderException $e) {
            $this->logger->error(sprintf('Error while fetching provider. Error was: %s.', $e->getMessage()), 'workflow', $contextDefinition->getName());
            return;
        }

        $this->warmUpProvider($contextDefinition, [$dataProvider, $indexProvider]);

        $this->logger->log(
            'DEBUG',
            sprintf('execute provider for dispatch type "%s"', $contextDefinition->getContextDispatchType()),
            $contextDefinition->getDataProviderName(),
            $contextDefinition->getName()
        );

        try {
            $dataProvider->provideAll($contextDefinition);
        } catch (ProcessCancelledException $e) {
            $errorMessage = sprintf('Process has been cancelled. Message was: %s. Process canceling has been initiated', $e->getMessage());
            $this->dispatchCancelledProcessToProviders($errorMessage, $contextDefinition, [$indexProvider, $dataProvider]);
        } catch (\Throwable $e) {
            $errorMessage = sprintf('Error while executing data provider. Error was: %s. FailOver has been initiated', $e->getMessage());
            $this->dispatchFailOverToProviders($errorMessage, $contextDefinition, [$indexProvider, $dataProvider]);
        }

        $this->coolDownProvider($contextDefinition, [$dataProvider, $indexProvider]);
    }
}