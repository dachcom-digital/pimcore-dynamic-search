<?php

namespace DynamicSearchBundle\Logger;

class Logger implements LoggerInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function log($level, $message, string $provider, string $contextName)
    {
        $this->logger->log($level, $message, ['provider' => $provider, 'contextName' => $contextName]);
    }

    /**
     * {@inheritDoc}
     */
    public function debug($message, string $provider, string $contextName)
    {
        $this->log('DEBUG', $message, $provider, $contextName);
    }

    /**
     * {@inheritDoc}
     */
    public function info($message, string $provider, string $contextName)
    {
        $this->log('INFO', $message, $provider, $contextName);
    }

    /**
     * {@inheritDoc}
     */
    public function warning($message, string $provider, string $contextName)
    {
        $this->log('WARNING', $message, $provider, $contextName);
    }

    /**
     * {@inheritDoc}
     */
    public function error($message, string $provider, string $contextName)
    {
        $this->log('ERROR', $message, $provider, $contextName);
    }
}