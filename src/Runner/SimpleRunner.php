<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Validator\ResourceValidatorInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Processor\ResourceDeletionProcessorInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use Pimcore\Model\Element\ElementInterface;

class SimpleRunner extends AbstractRunner implements SimpleRunnerInterface
{
    public function __construct(
        protected ResourceHarmonizerInterface $resourceHarmonizer,
        protected ResourceValidatorInterface $resourceValidator,
        protected ResourceDeletionProcessorInterface $resourceDeletionProcessor
    ) {
    }

    public function runInsert(string $contextName, mixed $resource): void
    {
        $this->runModification($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INSERT, $resource);
    }

    public function runUpdate(string $contextName, mixed $resource): void
    {
        $this->runModification($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE, $resource);
    }

    public function runDelete(string $contextName, mixed $resource): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INSERT);

        $this->resourceDeletionProcessor->process($contextDefinition, $resource);
    }

    /**
     * @throws SilentException
     */
    protected function runModification(string $contextName, string $contextDispatchType, mixed $resource): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, $contextDispatchType);

        if ($resource instanceof ElementInterface) {
            $resourceType = sprintf('%s-%s', $resource->getType(), $resource->getId());
        } elseif (is_object($resource)) {
            $resourceType = get_class($resource);
        } else {
            $resourceType = gettype($resource);
        }

        $normalizedResourceStack = $this->resourceHarmonizer->harmonizeUntilNormalizedResourceStack($contextDefinition, $resource);

        if ($normalizedResourceStack === null) {
            // nothing to log: done by harmonizer.
            return;
        }

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH);

        $this->warmUpProvider($contextDefinition, $providers);

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        foreach ($normalizedResourceStack as $normalizedDataResource) {
            $resourceMeta = $normalizedDataResource->getResourceMeta();

            if (empty($resourceMeta->getDocumentId())) {
                $this->logger->error(
                    sprintf('No valid document id for resource "%s" given. Skipping...', $resourceType),
                    'queue',
                    $contextName
                );

                continue;
            }

            $this->callSaveMethod($contextDefinition, $dataProvider, 'provideSingle', [$contextDefinition, $resourceMeta], $providers);
        }

        $this->coolDownProvider($contextDefinition, $providers);
    }
}
