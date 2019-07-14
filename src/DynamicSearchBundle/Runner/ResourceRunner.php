<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Processor\ResourceDeletionProcessorInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;

class ResourceRunner extends AbstractRunner implements ResourceRunnerInterface
{
    /**
     * @var ResourceDeletionProcessorInterface
     */
    protected $resourceDeletionProcessor;

    /**
     * @param ResourceDeletionProcessorInterface $resourceDeletionProcessor
     */
    public function __construct(ResourceDeletionProcessorInterface $resourceDeletionProcessor)
    {
        $this->resourceDeletionProcessor = $resourceDeletionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function runInsert(string $contextName, ResourceMetaInterface $resourceMeta)
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDataInterface::CONTEXT_DISPATCH_TYPE_INSERT);

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH);

        $this->warmUpProvider($contextDefinition, $providers);

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        $this->callSaveMethod($contextDefinition, $dataProvider, 'provideSingle', [$contextDefinition, $resourceMeta], $providers);

        $this->coolDownProvider($contextDefinition, $providers);
    }

    /**
     * {@inheritdoc}
     */
    public function runInsertStack(string $contextName, array $resourceMetaStack)
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDataInterface::CONTEXT_DISPATCH_TYPE_INSERT);

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH);

        $this->warmUpProvider($contextDefinition, $providers);

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        foreach ($resourceMetaStack as $resourceMeta) {
            $this->callSaveMethod($contextDefinition, $dataProvider, 'provideSingle', [$contextDefinition, $resourceMeta], $providers);
        }

        $this->coolDownProvider($contextDefinition, $providers);
    }

    /**
     * {@inheritdoc}
     */
    public function runUpdate(string $contextName, ResourceMetaInterface $resourceMeta)
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDataInterface::CONTEXT_DISPATCH_TYPE_UPDATE);

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH);

        $this->warmUpProvider($contextDefinition, $providers);

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        $this->callSaveMethod($contextDefinition, $dataProvider, 'provideSingle', [$contextDefinition, $resourceMeta], $providers);

        $this->coolDownProvider($contextDefinition, $providers);
    }

    /**
     * {@inheritdoc}
     */
    public function runUpdateStack(string $contextName, array $resourceMetaStack)
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDataInterface::CONTEXT_DISPATCH_TYPE_UPDATE);

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH);

        $this->warmUpProvider($contextDefinition, $providers);

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        foreach ($resourceMetaStack as $resourceMeta) {
            $this->callSaveMethod($contextDefinition, $dataProvider, 'provideSingle', [$contextDefinition, $resourceMeta], $providers);
        }

        $this->coolDownProvider($contextDefinition, $providers);
    }

    /**
     * {@inheritdoc}
     */
    public function runDelete(string $contextName, ResourceMetaInterface $resourceMeta)
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE);

        $indexProvider = $this->setupIndexProvider($contextDefinition);
        if (!$indexProvider instanceof IndexProviderInterface) {
            return;
        }

        $this->warmUpProvider($contextDefinition, [$indexProvider]);

        $this->callSaveMethod($contextDefinition, $this->resourceDeletionProcessor, 'processByResourceMeta', [$contextDefinition, $resourceMeta], [$indexProvider]);

        $this->coolDownProvider($contextDefinition, [$indexProvider]);
    }

    /**
     * {@inheritdoc}
     */
    public function runDeleteStack(string $contextName, array $resourceMetaStack)
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE);

        $indexProvider = $this->setupIndexProvider($contextDefinition);

        $this->warmUpProvider($contextDefinition, [$indexProvider]);

        foreach ($resourceMetaStack as $resourceMeta) {
            $this->callSaveMethod($contextDefinition, $this->resourceDeletionProcessor, 'processByResourceMeta', [$contextDefinition, $resourceMeta], [$indexProvider]);
        }

        $this->coolDownProvider($contextDefinition, [$indexProvider]);
    }
}
