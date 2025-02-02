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

namespace DynamicSearchBundle\Logger;

class Logger implements LoggerInterface
{
    public function __construct(protected \Psr\Log\LoggerInterface $logger)
    {
    }

    public function getPsrLogger(): \Psr\Log\LoggerInterface
    {
        return $this->logger;
    }

    public function log(mixed $level, string $message, string $provider, string $contextName): void
    {
        $this->logger->log($level, $message, ['provider' => $provider, 'contextName' => $contextName]);
    }

    public function debug(string $message, string $provider, string $contextName): void
    {
        $this->log('DEBUG', $message, $provider, $contextName);
    }

    public function info(string $message, string $provider, string $contextName): void
    {
        $this->log('INFO', $message, $provider, $contextName);
    }

    public function warning(string $message, string $provider, string $contextName): void
    {
        $this->log('WARNING', $message, $provider, $contextName);
    }

    public function error(string $message, string $provider, string $contextName): void
    {
        $this->log('ERROR', $message, $provider, $contextName);
    }
}
