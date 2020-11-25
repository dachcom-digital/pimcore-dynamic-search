<?php

namespace DynamicSearchBundle\Logger;

interface LoggerInterface
{
    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getPsrLogger();

    /**
     * DEBUG (100)
     * INFO (200)
     * NOTICE (250)
     * WARNING (300)
     * ERROR (400)
     * CRITICAL (500)
     * ALERT (550)
     * EMERGENCY (600).
     *
     * @param string $level
     * @param string $message
     * @param string $provider
     * @param string $contextName
     */
    public function log(string $level, string $message, string $provider, string $contextName);

    /**
     * @param string $message
     * @param string $provider
     * @param string $contextName
     */
    public function debug($message, string $provider, string $contextName);

    /**
     * @param string $message
     * @param string $provider
     * @param string $contextName
     */
    public function info($message, string $provider, string $contextName);

    /**
     * @param string $message
     * @param string $provider
     * @param string $contextName
     */
    public function warning($message, string $provider, string $contextName);

    /**
     * @param string $message
     * @param string $provider
     * @param string $contextName
     */
    public function error($message, string $provider, string $contextName);
}
