<?php

namespace DynamicSearchBundle\Manager;

interface QueueManagerInterface
{
    public function getQueueTableName(): string;
    public function getTotalQueuedItems(): int;
    public function clearQueue(): void;
}
