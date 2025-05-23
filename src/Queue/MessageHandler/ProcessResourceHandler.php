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
    ) {
    }

    public function __invoke(ProcessResourceMessage $message, ?Acknowledger $ack)
    {
        return $this->handle($message, $ack);
    }

    private function process(array $jobs): void
    {
        $processableJobs = [];
        $groupedResourceMetas = [];
        $existingKeys = [];

        /**
         * @var ProcessResourceMessage $message
         * @var Acknowledger           $ack
         */
        foreach ($jobs as [$message, $ack]) {
            $processableJobs[] = $message;
            $ack->ack($message);
        }

        /*
        * A resource can be added multiple times (saving an element 3 or more times in short intervals for example).
        * Only the latest resource of its kind should be used in index processing to improve performance.
        */
        $processableJobs = array_reverse($processableJobs);

        /** @var ProcessResourceMessage $message */
        foreach ($processableJobs as $message) {
            if (!isset($groupedResourceMetas[$message->contextName])) {
                $groupedResourceMetas[$message->contextName] = [];
            }

            if (!isset($groupedResourceMetas[$message->contextName][$message->dispatchType])) {
                $groupedResourceMetas[$message->contextName][$message->dispatchType] = [];
            }

            $key = sprintf('%s_%s', $message->contextName, $message->resourceMeta->getDocumentId());

            if (in_array($key, $existingKeys, true)) {
                continue;
            }

            $groupedResourceMetas[$message->contextName][$message->dispatchType][] = $message->resourceMeta;

            $existingKeys[] = $key;
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
