<?php

namespace DynamicSearchBundle\Service;

use Pimcore\Model\Tool\TmpStore;

class LockService implements LockServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function isLocked(string $token)
    {
        $tmpStore = $this->getLockToken($token);

        return $tmpStore instanceof TmpStore;
    }

    /**
     * {@inheritdoc}
     */
    public function getLockMessage(string $token)
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

    /**
     * {@inheritdoc}
     */
    public function lock(string $token, string $executor, $lifeTime = 14400)
    {
        if ($this->isLocked($token)) {
            return;
        }

        TmpStore::add($this->getNamespacedToken($token), $executor, null, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function unlock(string $token)
    {
        TmpStore::delete($this->getNamespacedToken($token));
    }

    /**
     * @param string $token
     *
     * @return TmpStore|null
     */
    protected function getLockToken(string $token)
    {
        $key = TmpStore::get($this->getNamespacedToken($token));

        return $key;
    }

    /**
     * @param string $token
     * @param string $namespace
     *
     * @return string
     */
    protected function getNamespacedToken(string $token, string $namespace = 'dynamic_search')
    {
        return sprintf('%s_%s', $namespace, $token);
    }
}
