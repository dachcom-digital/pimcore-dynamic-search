<?php

namespace DynamicSearchBundle\Service;

interface LockServiceInterface
{
    public const CONTEXT_INDEXING = 'context_indexing';

    public const QUEUE_INDEXING = 'queue_indexing';

    public function isLocked(string $token): bool;

    public function getLockMessage(string $token): string;

    public function lock(string $token, string $executor, int $lifeTime = 14400): void;

    public function unlock(string $token): void;
}
