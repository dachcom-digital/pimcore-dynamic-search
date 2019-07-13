<?php

namespace DynamicSearchBundle\Command\Traits;

trait SignalWatchTrait
{
    protected $key;

    /**
     * @param string|null $lockKey
     */
    public function watchSignalWithLockKey($lockKey = null)
    {
        $this->key = $lockKey;

        $this->watchSignal('parseProcessControlSignalWithLockKey');
    }

    public function watchSignal($dispatchMethod = 'parseProcessControlSignal')
    {
        if (!function_exists('pcntl_signal')) {
            return;
        }

        pcntl_signal(SIGTERM, [$this, $dispatchMethod]);
        pcntl_signal(SIGINT, [$this, $dispatchMethod]);
        pcntl_signal(SIGHUP, [$this, $dispatchMethod]);
        pcntl_signal(SIGQUIT, [$this, $dispatchMethod]);
    }

    public function parseProcessControlSignal()
    {
        // implement logic in your service.
    }

    public function parseProcessControlSignalWithLockKey()
    {
        if (is_null($this->key)) {
            return;
        }

        $this->lockService->unlock($this->key);
    }

    protected function dispatchProcessControlSignal()
    {
        pcntl_signal_dispatch();
    }
}
