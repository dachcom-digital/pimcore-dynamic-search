<?php

namespace DynamicSearchBundle\Resource;

class ResourceScaffolderContainer implements ResourceScaffolderContainerInterface
{
    public function __construct(
        protected ResourceScaffolderInterface $scaffolder,
        protected string $identifier
    ) {
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
