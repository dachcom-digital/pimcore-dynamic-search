<?php

namespace DynamicSearchBundle\Validator;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\ResourceCandidateEvent;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\DataProviderValidationAwareInterface;
use DynamicSearchBundle\Resource\ResourceCandidate;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResourceValidator implements ResourceValidatorInterface
{
    /**
     * @var ContextDefinitionBuilderInterface
     */
    protected $contextDefinitionBuilder;

    /**
     * @var DataManagerInterface
     */
    protected $dataManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param ContextDefinitionBuilderInterface $contextDefinitionBuilder
     * @param DataManagerInterface              $dataManager
     * @param EventDispatcherInterface          $eventDispatcher
     */
    public function __construct(
        ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        DataManagerInterface $dataManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->contextDefinitionBuilder = $contextDefinitionBuilder;
        $this->dataManager = $dataManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function checkUntrustedResourceProxy(string $contextName, string $dispatchType, $resource)
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);
        $dataProvider = $this->getDataProvider($contextName, $dispatchType);

        if (!$dataProvider instanceof DataProviderInterface) {
            return null;
        }

        return $dataProvider->checkUntrustedResourceProxy($contextDefinition, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function validateUntrustedResource(string $contextName, string $dispatchType, $resource)
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);
        $dataProvider = $this->getDataProvider($contextName, $dispatchType);

        if (!$dataProvider instanceof DataProviderInterface) {
            return false;
        }

        return $dataProvider->validateUntrustedResource($contextDefinition, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function validateResource(string $contextName, string $dispatchType, bool $isUnknownResource, bool $isImmutableResource, $resource)
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);
        $dataProvider = $this->getDataProvider($contextName, $dispatchType);

        if (!$dataProvider instanceof DataProviderValidationAwareInterface) {
            return null;
        }

        $resourceCandidate = new ResourceCandidate($contextName, $dispatchType, $isUnknownResource === true, $isImmutableResource === false, $resource);

        $dataProvider->validateResource($contextDefinition, $resourceCandidate);

        // allow third-party hooks
        $resourceCandidateEvent = new ResourceCandidateEvent($resourceCandidate);
        $this->eventDispatcher->dispatch($resourceCandidateEvent, DynamicSearchEvents::RESOURCE_CANDIDATE_VALIDATION);

        return $resourceCandidateEvent->getResourceCandidate();
    }

    /**
     * @param string $contextName
     * @param string $dispatchType
     *
     * @return bool|DataProviderInterface
     */
    protected function getDataProvider(string $contextName, string $dispatchType)
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);

        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH);
        } catch (ProviderException $e) {
            return false;
        }

        return $dataProvider;
    }
}
