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

namespace DynamicSearchBundle\Command\Traits;

trait SignalWatchTrait
{
    protected ?string $key;

    public function watchSignalWithLockKey(?string $lockKey = null): void
    {
        $this->key = $lockKey;

        $this->watchSignal('parseProcessControlSignalWithLockKey');
    }

    public function watchSignal($dispatchMethod = 'parseProcessControlSignal'): void
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
