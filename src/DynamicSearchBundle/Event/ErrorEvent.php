<?php

namespace DynamicSearchBundle\Event;

use DynamicSearchBundle\Context\ContextDataInterface;
use Symfony\Component\EventDispatcher\Event;

class ErrorEvent extends Event
{
    /**
     * @var ContextDataInterface
     */
    protected $contextData;

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
     * @param ContextDataInterface $contextData
     * @param string               $message
     * @param string|null          $providerName
     * @param \Exception|null      $exception
     */
    public function __construct(ContextDataInterface $contextData, string $message, string $providerName = null, \Exception $exception = null)
    {
        $this->contextData = $contextData;
        $this->providerName = $providerName;
        $this->message = $message;
        $this->exception = $exception;
    }

    /**
     * @return ContextDataInterface
     */
    public function getContextData()
    {
        return $this->contextData;
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
