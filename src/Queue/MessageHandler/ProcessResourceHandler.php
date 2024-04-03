<?php

namespace DynamicSearchBundle\Queue\MessageHandler;

use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Queue\Message\ProcessResourceMessage;
use DynamicSearchBundle\Runner\ResourceRunnerInterface;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;

class ProcessResourceHandler implements BatchHandlerInterface
{

    use BatchHandlerTrait;

    public function __construct(
        protected LoggerInterface $logger,
        protected ResourceRunnerInterface $resourceRunner
    )
    {}

    public function __invoke(ProcessResourceMessage $message, ?Acknowledger $ack)
    {
        return $this->handle($message, $ack);
    }

    private function process(array $jobs): void
    {
        $groupedResourceMetas = [];

        /**
         * @var ProcessResourceMessage $message
         * @var Acknowledger $ack
         */
        foreach ($jobs as [$message, $ack]) {
            if (!isset($groupedResourceMetas[$message->contextName])) {
                $groupedResourceMetas[$message->contextName] = [];
            }
            if (!isset($groupedResourceMetas[$message->contextName][$message->dispatchType])) {
                $groupedResourceMetas[$message->contextName][$message->dispatchType] = [];
            }
            $groupedResourceMetas[$message->contextName][$message->dispatchType][] = $message->resourceMeta;
            $ack->ack($message);
        }

        foreach ($groupedResourceMetas as $contextName => $contextResourceMetas) {
            foreach ($contextResourceMetas as $dispatchType => $resourceMetas) {
                try {
                    $this->resourceRunner->runResourceStack($contextName, $dispatchType, $resourceMetas);
                } catch (SilentException $e) {
                    // do not raise errors in silent exception. this error has been logged already in the right channel.
                } catch (\Throwable $e) {
                    $this->logger->error(
                        sprintf('Error dispatch resource (%s). Message was: %s', $dispatchType, $e->getMessage()),
                        'queue',
                        $contextName
                    );
                }
            }
        }
    }

    private function getBatchSize(): int
    {
        return 50;
    }
}
