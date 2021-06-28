<?php

namespace DynamicSearchBundle\Service;

use Pimcore\Model\Tool\TmpStore;

class LockService implements LockServiceInterface
{
    public function isLocked(string $token): bool
    {
        $tmpStore = $this->getLockToken($token);

        return $tmpStore instanceof TmpStore;
    }

    public function getLockMessage(string $token): string
    {
        if (!$this->isLocked($token)) {
            return 'not-locked';
        }

        $tmpStore = $this->getLockToken($token);
        $startDate = date('m-d-Y H:i:s', $tmpStore->getDate());
        $failOverDate = date('m-d-Y H:i:s', $tmpStore->getExpiryDate());
        $executor = $tmpStore->getData();

        return sprintf(
            'Process "%s" has been locked at %s by "%s" and will stay locked until process is finished with a self-delete failover at %s',
            $token,
            $startDate,
            $executor,
            $failOverDate
        );
    }

    public function lock(string $token, string $executor, int $lifeTime = 14400): void
    {
        if ($this->isLocked($token)) {
            return;
        }

        TmpStore::add($this->getNamespacedToken($token), $executor, null, $lifeTime);
    }

    public function unlock(string $token): void
    {
        TmpStore::delete($this->getNamespacedToken($token));
    }

    protected function getLockToken(string $token): ?TmpStore
    {
        return TmpStore::get($this->getNamespacedToken($token));
    }

    protected function getNamespacedToken(string $token, string $namespace = 'dynamic_search'): string
    {
        return sprintf('%s_%s', $namespace, $token);
    }
}
