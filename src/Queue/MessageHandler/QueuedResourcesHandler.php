<?php

namespace DynamicSearchBundle\Queue\MessageHandler;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Queue\Message\ProcessResourceMessage;
use DynamicSearchBundle\Queue\Message\QueueResourceMessage;
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
    )
    {}

    public function __invoke(QueueResourceMessage $message, ?Acknowledger $ack = null)
    {
        return $this->handle($message, $ack);
    }

    private function process(array $jobs): void
    {
        /**
         * @var QueueResourceMessage $message
         * @var Acknowledger $ack
         */
        foreach ($jobs as [$message, $ack]) {
            try {

                $resource = $message->resource;
                // @todo: use introduced "resource info" dto to determinate resource / type
                if (str_contains($message->resourceType, '-')) {
                    [$type, $id] = explode('-', $message->resourceType);
                    if (is_numeric($id)) {
                        $id = (int) $id;
                    }
                    $resource = Element\Service::getElementById($type, $id);
                    if ($resource === null && $message->dispatchType === ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE) {
                        // at this time, the resource is already deleted by pimcore
                        // since we do not serialize the resource into the message,
                        // we need to create a dummy resource to retrieve a valid resource meta for deletion
                        $resource = match ($type) {
                            'document' => new Document(),
                            'asset' => new Asset(),
                            'object' => new DataObject\Concrete(),
                        };
                        $resource->setId($id);
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
