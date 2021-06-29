<?php

namespace DynamicSearchBundle\Resource;

class ResourceScaffolderContainer implements ResourceScaffolderContainerInterface
{
    protected ResourceScaffolderInterface $scaffolder;
    protected string $identifier;

    public function __construct(ResourceScaffolderInterface $scaffolder, string $identifier)
    {
        $this->scaffolder = $scaffolder;
        $this->identifier = $identifier;
    }

    public function getScaffolder(): ResourceScaffolderInterface
    {
        return $this->scaffolder;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
