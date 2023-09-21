<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Processor\ResourceDeletionProcessorInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;

class ResourceRunner extends AbstractRunner implements ResourceRunnerInterface
{
    public function __construct(protected ResourceDeletionProcessorInterface $resourceDeletionProcessor)
    {
    }

    public function runInsert(string $contextName, ResourceMetaInterface $resourceMeta): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INSERT);

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH);

        $this->warmUpProvider($contextDefinition, $providers);

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        $this->callSaveMethod($contextDefinition, $dataProvider, 'provideSingle', [$contextDefinition, $resourceMeta], $providers);
        $this->coolDownProvider($contextDefinition, $providers);
    }

    public function runInsertStack(string $contextName, array $resourceMetaStack): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INSERT);

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH);

        $this->warmUpProvider($contextDefinition, $providers);

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        foreach ($resourceMetaStack as $resourceMeta) {
            $this->callSaveMethod($contextDefinition, $dataProvider, 'provideSingle', [$contextDefinition, $resourceMeta], $providers);
        }

        $this->coolDownProvider($contextDefinition, $providers);
    }

    public function runUpdate(string $contextName, ResourceMetaInterface $resourceMeta): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE);

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH);

        $this->warmUpProvider($contextDefinition, $providers);

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        $this->callSaveMethod($contextDefinition, $dataProvider, 'provideSingle', [$contextDefinition, $resourceMeta], $providers);
        $this->coolDownProvider($contextDefinition, $providers);
    }

    public function runUpdateStack(string $contextName, array $resourceMetaStack): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE);

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH);

        $this->warmUpProvider($contextDefinition, $providers);

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        foreach ($resourceMetaStack as $resourceMeta) {
            $this->callSaveMethod($contextDefinition, $dataProvider, 'provideSingle', [$contextDefinition, $resourceMeta], $providers);
        }

        $this->coolDownProvider($contextDefinition, $providers);
    }

    public function runDelete(string $contextName, ResourceMetaInterface $resourceMeta): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE);

        $indexProvider = $this->setupIndexProvider($contextDefinition);
        if (!$indexProvider instanceof IndexProviderInterface) {
            return;
        }

        $this->warmUpProvider($contextDefinition, [$indexProvider]);
        $this->callSaveMethod($contextDefinition, $this->resourceDeletionProcessor, 'processByResourceMeta', [$contextDefinition, $resourceMeta], [$indexProvider]);
        $this->coolDownProvider($contextDefinition, [$indexProvider]);
    }

    public function runDeleteStack(string $contextName, array $resourceMetaStack): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE);

        $indexProvider = $this->setupIndexProvider($contextDefinition);

        $this->warmUpProvider($contextDefinition, [$indexProvider]);

        foreach ($resourceMetaStack as $resourceMeta) {
            $this->callSaveMethod($contextDefinition, $this->resourceDeletionProcessor, 'processByResourceMeta', [$contextDefinition, $resourceMeta], [$indexProvider]);
        }

        $this->coolDownProvider($contextDefinition, [$indexProvider]);
    }
}
