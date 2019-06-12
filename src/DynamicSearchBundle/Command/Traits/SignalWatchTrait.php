<?php

namespace DynamicSearchBundle\Command\Traits;

trait SignalWatchTrait
{
    protected $key;

    /**
     * @param string $key
     */
    public function watchSignal($key)
    {
        $this->key = $key;

        pcntl_signal(SIGTERM, [$this, 'stopCommand']);
        pcntl_signal(SIGINT, [$this, 'stopCommand']);
        pcntl_signal(SIGHUP, [$this, 'stopCommand']);
        pcntl_signal(SIGQUIT, [$this, 'stopCommand']);
        pcntl_signal(SIGKILL, [$this, 'stopCommand']);
    }

    public function stopCommand()
    {
        $this->lockService->unlock($this->key);
    }
}
