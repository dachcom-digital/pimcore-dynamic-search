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

namespace DynamicSearchBundle\Queue\MessageHandler;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Queue\Message\ProcessResourceMessage;
use DynamicSearchBundle\Queue\Message\QueueResourceMessage;
use DynamicSearchBundle\Resource\ResourceInfoInterface;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueuedResourcesHandler implements BatchHandlerInterface
{
    use BatchHandlerTrait;

    public function __construct(
        protected LoggerInterface $logger,
        protected ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        protected ResourceHarmonizerInterface $resourceHarmonizer,
        protected MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(QueueResourceMessage $message, ?Acknowledger $ack = null)
    {
        return $this->handle($message, $ack);
    }

    private function process(array $jobs): void
    {
        /**
         * @var QueueResourceMessage $message
         * @var Acknowledger         $ack
         */
        foreach ($jobs as [$message, $ack]) {
            try {
                $resource = $message->resource;

                if ($message->resourceType === ResourceInfoInterface::TYPE_PIMCORE_ELEMENT) {
                    $resourceInfo = $resource;
                    if (!$resourceInfo instanceof ResourceInfoInterface) {
                        $this->logger->error(
                            'Unable to get resource info for pimcore resource.',
                            'queue',
                            $message->contextName
                        );

                        $ack->ack($message);

                        continue;
                    }

                    $resource = Element\Service::getElementById($resourceInfo->getResourceType(), $resourceInfo->getResourceId());
                    if ($resource === null && $message->dispatchType === ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE) {
                        // at this time, the resource is already deleted by pimcore
                        // since we do not serialize the resource into the message,
                        // we need to create a fake resource in order for the resource normalizer
                        // to generate a valid resource meta for deletion
                        $resource = match ($resourceInfo->getResourceType()) {
                            'document' => new Document(),
                            'asset'    => new Asset(),
                            'object'   => new DataObject\Concrete(),
                            default    => null,
                        };

                        $resource?->setId($resourceInfo->getResourceId());

                        if ($resource instanceof Document && null !== $locale = $resourceInfo->getResourceLocale()) {
                            $resource->setProperty('language', 'text', $locale, false, true);
                        }
                    }
                }

                $normalizedResourceStack = $this->generateResourceMeta($message->contextName, $message->dispatchType, $resource);

                if (count($normalizedResourceStack) === 0) {
                    $this->logger->error(
                        sprintf('Unable to assert stack for resource "%s". No queue job will be generated.', $message->resourceType),
                        'queue',
                        $message->contextName
                    );

                    $ack->ack($message);

                    continue;
                }

                foreach ($normalizedResourceStack as $normalizedDataResource) {
                    $resourceMeta = $normalizedDataResource->getResourceMeta();

                    if (empty($resourceMeta->getDocumentId())) {
                        $this->logger->error(
                            sprintf('No valid document id for resource "%s" given. Skipping...', $message->resourceType),
                            'queue',
                            $message->contextName
                        );

                        $ack->ack($message);

                        continue;
                    }

                    $this->messageBus->dispatch(new ProcessResourceMessage($message->contextName, $message->dispatchType, $resourceMeta));
                }

                $ack->ack($message);
            } catch (\Throwable $e) {
                $ack->nack($e);
            }
        }
    }

    /**
     * @return array<int, NormalizedDataResourceInterface>
     */
    protected function generateResourceMeta(string $contextName, string $dispatchType, mixed $resource): array
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);

        $normalizedResourceStack = $this->resourceHarmonizer->harmonizeUntilNormalizedResourceStack($contextDefinition, $resource);

        if ($normalizedResourceStack === null) {
            // nothing to log: done by harmonizer.
            return [];
        }

        return $normalizedResourceStack;
    }

    private function getBatchSize(): int
    {
        return 50;
    }
}
