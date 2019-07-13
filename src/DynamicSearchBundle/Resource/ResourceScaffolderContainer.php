<?php

namespace DynamicSearchBundle\Resource;

class ResourceScaffolderContainer implements ResourceScaffolderContainerInterface
{
    /**
     * @var ResourceScaffolderInterface
     */
    protected $scaffolder;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @param ResourceScaffolderInterface $scaffolder
     * @param string                      $identifier
     */
    public function __construct(ResourceScaffolderInterface $scaffolder, string $identifier)
    {
        $this->scaffolder = $scaffolder;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getScaffolder()
    {
        return $this->scaffolder;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
