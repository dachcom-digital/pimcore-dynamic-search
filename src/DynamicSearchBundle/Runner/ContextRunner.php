<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Manager\QueueManagerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Service\LongProcessServiceInterface;

class ContextRunner extends AbstractRunner implements ContextRunnerInterface
{
    protected QueueManagerInterface $queueManager;
    protected LongProcessServiceInterface $longProcessService;

    public function __construct(
        QueueManagerInterface $queueManager,
        LongProcessServiceInterface $longProcessService
    ) {
        $this->queueManager = $queueManager;
        $this->longProcessService = $longProcessService;
    }

    public function runFullContextCreation(): void
    {
        $contextDefinitions = $this->contextDefinitionBuilder->buildContextDefinitionStack(ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INDEX);

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

    public function runSingleContextCreation(string $contextName): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INDEX);

        $this->queueManager->clearQueue();
        $this->longProcessService->boot();

        $this->dispatchContext($contextDefinition);

        $this->longProcessService->shutdown();
    }

    /**
     * @throws SilentException
     */
    protected function dispatchContext(ContextDefinitionInterface $contextDefinition): void
    {
        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH);

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
