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
     * @param QueueManagerInterface       $queueManager
     * @param LongProcessServiceInterface $longProcessService
     */
    public function __construct(
        QueueManagerInterface $queueManager,
        LongProcessServiceInterface $longProcessService
    ) {
        $this->queueManager = $queueManager;
        $this->longProcessService = $longProcessService;
    }

    /**
     * {@inheritdoc}
     */
    public function runFullContextCreation()
    {
        $contextDefinitions = $this->configuration->getContextDefinitions(ContextDataInterface::CONTEXT_DISPATCH_TYPE_INDEX);

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
     * {@inheritdoc}
     */
    public function runSingleContextCreation(string $contextName)
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDataInterface::CONTEXT_DISPATCH_TYPE_INDEX);

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

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH);

        if ($providers === null) {
            return;
        }

        $this->warmUpProvider($contextDefinition, $providers);

        $this->logger->log(
            'DEBUG',
            sprintf('execute provider for dispatch type "%s"', $contextDefinition->getContextDispatchType()),
            $contextDefinition->getDataProviderName(),
            $contextDefinition->getName()
        );

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        $this->callSaveMethod($contextDefinition, $dataProvider, 'provideAll', [$contextDefinition], $providers);

        $this->coolDownProvider($contextDefinition, $providers);
    }
}
