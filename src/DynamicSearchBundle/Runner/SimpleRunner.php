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
    /**
     * @var ResourceHarmonizerInterface
     */
    protected $resourceHarmonizer;

    /**
     * @var ResourceValidatorInterface
     */
    protected $resourceValidator;

    /**
     * @var ResourceDeletionProcessorInterface
     */
    protected $resourceDeletionProcessor;

    /**
     * @param ResourceHarmonizerInterface        $resourceHarmonizer
     * @param ResourceValidatorInterface         $resourceValidator
     * @param ResourceDeletionProcessorInterface $resourceDeletionProcessor
     */
    public function __construct(
        ResourceHarmonizerInterface $resourceHarmonizer,
        ResourceValidatorInterface $resourceValidator,
        ResourceDeletionProcessorInterface $resourceDeletionProcessor
    ) {
        $this->resourceHarmonizer = $resourceHarmonizer;
        $this->resourceValidator = $resourceValidator;
        $this->resourceDeletionProcessor = $resourceDeletionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function runInsert(string $contextName, $resource)
    {
        $this->runModification($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INSERT, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function runUpdate(string $contextName, $resource)
    {
        $this->runModification($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function runDelete(string $contextName, $resource)
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INSERT);

        $this->resourceDeletionProcessor->process($contextDefinition, $resource);
    }

    /**
     * @param string $contextName
     * @param string $contextDispatchType
     * @param mixed  $resource
     *
     * @throws SilentException
     */
    protected function runModification(string $contextName, string $contextDispatchType, $resource)
    {
        $contextDefinition = $this->setupContextDefinition($contextName, $contextDispatchType);

        if ($resource instanceof ElementInterface) {
            $resourceType = sprintf('%s-%s', $resource->getType(), $resource->getId());
        } elseif (is_object($resource)) {
            $resourceType = get_class($resource);
        } else {
            $resourceType = gettype($resource);
        }

        $resourcedIsValid = $this->resourceValidator->validateUntrustedResource($contextDefinition->getName(), $contextDefinition->getContextDispatchType(), $resource);

        if ($resourcedIsValid === false) {
            $this->logger->debug(
                sprintf('Resource has been marked as untrusted. Skipping...'),
                $contextDefinition->getDataProviderName(),
                $contextDefinition->getName()
            );

            return;
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
