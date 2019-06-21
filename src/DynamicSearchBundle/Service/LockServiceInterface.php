<?php

namespace DynamicSearchBundle\Service;

interface LockServiceInterface
{
    const CONTEXT_INDEXING = 'context_indexing';

    const QUEUE_INDEXING = 'queue_indexing';

    /**
     * @param string $token
     *
     * @return bool
     */
    public function isLocked(string $token);

    /**
     * @param string $token
     *
     * @return string
     */
    public function getLockMessage(string $token);

    /**
     * @param string $token
     * @param string $executor
     * @param int    $lifeTime default 4h
     */
    public function lock(string $token, string $executor, $lifeTime = 14400);

    /**
     * @param string $token
     */
    public function unlock(string $token);
}
