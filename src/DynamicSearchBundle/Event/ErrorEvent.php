<?php

namespace DynamicSearchBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ErrorEvent extends Event
{
    protected string $contextName;
    protected ?string $providerName = null;
    protected string $message;
    protected ?\Exception $exception;

    public function __construct(string $contextName, string $message, string $providerName = null, \Exception $exception = null)
    {
        $this->contextName = $contextName;
        $this->providerName = $providerName;
        $this->message = $message;
        $this->exception = $exception;
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function getProviderName(): ?string
    {
        return $this->providerName;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getException(): ?\Exception
    {
        return $this->exception;
    }
}
