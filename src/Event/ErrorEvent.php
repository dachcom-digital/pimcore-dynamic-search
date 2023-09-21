<?php

namespace DynamicSearchBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ErrorEvent extends Event
{

    public function __construct(
        protected string $contextName,
        protected string $message,
        protected ?string $providerName = null,
        protected ?\Exception $exception = null
    ) {
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
