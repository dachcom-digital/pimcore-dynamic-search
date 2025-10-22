<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace DynamicSearchBundle\Queue;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Queue\Message\QueueResourceMessage;
use DynamicSearchBundle\Resource\ResourceInfo;
use DynamicSearchBundle\Resource\ResourceInfoInterface;
use DynamicSearchBundle\Service\LockServiceInterface;
use DynamicSearchBundle\Validator\ResourceValidatorInterface;
use Pimcore\Model\Document;
use Pimcore\Model\Element;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DataCollector implements DataCollectorInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        protected ResourceValidatorInterface $resourceValidator,
        protected MessageBusInterface $messageBus,
        protected LockServiceInterface $lockService
    ) {
    }

    public function addToGlobalQueue(string $dispatchType, mixed $resource, array $options = []): void
    {
        $contextDefinitions = $this->contextDefinitionBuilder->buildContextDefinitionStack(ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INDEX);

        if (count($contextDefinitions) === 0) {
            $this->logger->error(
                'No context configuration found. Please add them to the "dynamic_search.context" configuration node',
                'queue',
                'global'
            );

            return;
        }

        foreach ($contextDefinitions as $contextDefinition) {
            $this->addToContextQueue($contextDefinition->getName(), $dispatchType, $resource, $options);
        }
    }

    public function addToContextQueue(string $contextName, string $dispatchType, mixed $resource, array $options = []): void
    {
        $isUnknownResource = true;
        $isImmutableResource = false;
        $resourceValidationOptions = $options['resourceValidation'] ?? null;
        if (is_array($resourceValidationOptions)) {
            $isUnknownResource = $resourceValidationOptions['unknownResource'] ?? $isUnknownResource;
            $isImmutableResource = $resourceValidationOptions['immutableResource'] ?? $isImmutableResource;
        }

        try {
            // validate and allow rewriting dispatch type and/or resource
            $resourceCandidate = $this->resourceValidator->validateResource($contextName, $dispatchType, $isUnknownResource, $isImmutableResource, $resource);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Error while validate resource candidate: %s', $e->getMessage()), 'queue', $contextName);

            return;
        }

        if ($resourceCandidate->getResource() === null) {
            $this->logger->debug(
                sprintf('Resource has been removed due to validation. Skipping...'),
                'queue',
                $contextName
            );

            return;
        }

        $resource = $resourceCandidate->getResource();
        $dispatchType = $resourceCandidate->getDispatchType();

        if (!in_array($dispatchType, ContextDefinitionInterface::ALLOWED_QUEUE_DISPATCH_TYPES, true)) {
            $this->logger->error(
                sprintf('Wrong dispatch type "%s" for queue. Allowed types are: %s', $dispatchType, join(', ', ContextDefinitionInterface::ALLOWED_QUEUE_DISPATCH_TYPES)),
                'queue',
                $contextName
            );

            return;
        }

        try {
            $this->generateJob($contextName, $dispatchType, $resource, $options);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf('Error while adding data to queue. Message was: %s', $e->getMessage()),
                'queue',
                $contextName
            );
        }
    }

    protected function generateJob(string $contextName, string $dispatchType, mixed $resource, array $options): void
    {
        // todo: create resource info factory
        if ($resource instanceof ElementInterface) {
            $resourceInfo = new ResourceInfo(
                $resource->getId(),
                Element\Service::getElementType($resource)
            );

            if ($resource instanceof Document && null !== $locale = $resource->getProperty('language')) {
                $resourceInfo->setResourceLocale($locale);
            }

            $resourceType = ResourceInfoInterface::TYPE_PIMCORE_ELEMENT;
            $resource = $resourceInfo;
        } elseif (is_object($resource)) {
            $resourceType = get_class($resource);
        } else {
            $resourceType = gettype($resource);
        }

        $this->messageBus->dispatch(new QueueResourceMessage($contextName, $dispatchType, $resourceType, $resource, $options));

        $this->logger->debug(
            sprintf('Envelope successfully added to queue ("%s" context)', $contextName),
            'queue',
            $contextName
        );
    }
}
