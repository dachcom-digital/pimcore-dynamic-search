<?php

namespace DynamicSearchBundle\Command\Traits;

trait SignalWatchTrait
{
    protected ?string $key = null;

    public function watchSignalWithLockKey(?string $lockKey = null): void
    {
        $this->key = $lockKey;

        $this->watchSignal('parseProcessControlSignalWithLockKey');
    }

    public function watchSignal(string $dispatchMethod = 'parseProcessControlSignal'): void
    {
        if (!function_exists('pcntl_signal')) {
            return;
        }

        pcntl_signal(SIGTERM, [$this, $dispatchMethod]);
        pcntl_signal(SIGINT, [$this, $dispatchMethod]);
        pcntl_signal(SIGHUP, [$this, $dispatchMethod]);
        pcntl_signal(SIGQUIT, [$this, $dispatchMethod]);
    }

    public function parseProcessControlSignal(): void
    {
        // implement logic in your service.
    }

    public function parseProcessControlSignalWithLockKey(): void
    {
        if (is_null($this->key)) {
            return;
        }

        $this->lockService->unlock($this->key);
    }

    protected function dispatchProcessControlSignal(): void
    {
        pcntl_signal_dispatch();
    }
}
