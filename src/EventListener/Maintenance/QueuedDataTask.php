<?php

namespace DynamicSearchBundle\EventListener\Maintenance;

use DynamicSearchBundle\Queue\DataProcessorInterface;
use Pimcore\Maintenance\TaskInterface;

class QueuedDataTask implements TaskInterface
{
    public function __construct(protected DataProcessorInterface $dataProcessor)
    {
    }

    public function execute(): void
    {
        $this->dataProcessor->process([]);
    }
}
