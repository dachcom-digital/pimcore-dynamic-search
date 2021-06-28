<?php

namespace DynamicSearchBundle\Validator;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\ResourceCandidateEvent;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\DataProviderValidationAwareInterface;
use DynamicSearchBundle\Resource\Proxy\ProxyResourceInterface;
use DynamicSearchBundle\Resource\ResourceCandidate;
use DynamicSearchBundle\Resource\ResourceCandidateInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ResourceValidator implements ResourceValidatorInterface
{
    protected ContextDefinitionBuilderInterface $contextDefinitionBuilder;
    protected DataManagerInterface $dataManager;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        DataManagerInterface $dataManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->contextDefinitionBuilder = $contextDefinitionBuilder;
        $this->dataManager = $dataManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function checkUntrustedResourceProxy(string $contextName, string $dispatchType, $resource): ?ProxyResourceInterface
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);
        $dataProvider = $this->getDataProvider($contextName, $dispatchType);

        if (!$dataProvider instanceof DataProviderInterface) {
            return null;
        }

        return $dataProvider->checkUntrustedResourceProxy($contextDefinition, $resource);
    }

    public function validateUntrustedResource(string $contextName, string $dispatchType, $resource): bool
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);
        $dataProvider = $this->getDataProvider($contextName, $dispatchType);

        if (!$dataProvider instanceof DataProviderInterface) {
            return false;
        }

        return $dataProvider->validateUntrustedResource($contextDefinition, $resource);
    }

    public function validateResource(string $contextName, string $dispatchType, bool $isUnknownResource, bool $isImmutableResource, $resource): ResourceCandidateInterface
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);
        $dataProvider = $this->getDataProvider($contextName, $dispatchType);

        $resourceCandidate = new ResourceCandidate($contextName, $dispatchType, $isUnknownResource === true, $isImmutableResource === false, $resource);

        if (!$dataProvider instanceof DataProviderValidationAwareInterface) {
            return $resourceCandidate;
        }

        $dataProvider->validateResource($contextDefinition, $resourceCandidate);

        // allow third-party hooks
        $resourceCandidateEvent = new ResourceCandidateEvent($resourceCandidate);
        $this->eventDispatcher->dispatch($resourceCandidateEvent, DynamicSearchEvents::RESOURCE_CANDIDATE_VALIDATION);

        return $resourceCandidateEvent->getResourceCandidate();
    }

    protected function getDataProvider(string $contextName, string $dispatchType): ?DataProviderInterface
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);

        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH);
        } catch (ProviderException $e) {
            return null;
        }

        return $dataProvider;
    }
}
