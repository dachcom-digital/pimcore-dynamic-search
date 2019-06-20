<?php

namespace DynamicSearchBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ErrorEvent extends Event
{
    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var string|null
     */
    protected $providerName;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @param string          $contextName
     * @param string          $message
     * @param string|null     $providerName
     * @param \Exception|null $exception
     */
    public function __construct(string $contextName, string $message, string $providerName = null, \Exception $exception = null)
    {
        $this->contextName = $contextName;
        $this->providerName = $providerName;
        $this->message = $message;
        $this->exception = $exception;
    }

    /**
     * @return string
     */
    public function getContextName()
    {
        return $this->contextName;
    }

    /**
     * @return string|null
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }
}
