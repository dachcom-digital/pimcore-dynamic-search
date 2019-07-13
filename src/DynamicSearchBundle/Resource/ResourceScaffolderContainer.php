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
     * {@inheritdoc}
     */
    public function getScaffolder()
    {
        return $this->scaffolder;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
