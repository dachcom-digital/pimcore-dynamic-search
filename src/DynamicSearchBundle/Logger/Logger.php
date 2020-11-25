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
     * {@inheritdoc}
     */
    public function getPsrLogger()
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, string $provider, string $contextName)
    {
        $this->logger->log($level, $message, ['provider' => $provider, 'contextName' => $contextName]);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, string $provider, string $contextName)
    {
        $this->log('DEBUG', $message, $provider, $contextName);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, string $provider, string $contextName)
    {
        $this->log('INFO', $message, $provider, $contextName);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, string $provider, string $contextName)
    {
        $this->log('WARNING', $message, $provider, $contextName);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, string $provider, string $contextName)
    {
        $this->log('ERROR', $message, $provider, $contextName);
    }
}
