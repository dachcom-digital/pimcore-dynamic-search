<?php

namespace DynamicSearchBundle\Queue\MessageHandler;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Queue\Message\ProcessResourceMessage;
use DynamicSearchBundle\Queue\Message\QueueResourceMessage;
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
                if (str_contains($message->resourceType, '-')) {
                    [$type, $id] = explode('-', $message->resourceType);
                    $resource = Element\Service::getElementById($type, $id);
                    if (!$resource instanceof Element\ElementInterface) {
                        continue;
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
