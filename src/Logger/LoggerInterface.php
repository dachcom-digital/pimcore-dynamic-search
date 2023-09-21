<?php

namespace DynamicSearchBundle\Logger;

interface LoggerInterface
{
    public function getPsrLogger(): \Psr\Log\LoggerInterface;

    /**
     * DEBUG (100)
     * INFO (200)
     * NOTICE (250)
     * WARNING (300)
     * ERROR (400)
     * CRITICAL (500)
     * ALERT (550)
     * EMERGENCY (600).
     */
    public function log(string $level, string $message, string $provider, string $contextName): void;

    public function debug(string $message, string $provider, string $contextName): void;

    public function info(string $message, string $provider, string $contextName): void;

    public function warning(string $message, string $provider, string $contextName): void;

    public function error(string $message, string $provider, string $contextName): void;
}
