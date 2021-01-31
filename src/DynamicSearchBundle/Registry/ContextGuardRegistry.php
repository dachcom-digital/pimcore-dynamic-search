<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Guard\ContextGuardInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class ContextGuardRegistry implements ContextGuardRegistryInterface
{
    /**
     * @var RegistryStorage
     */
    protected $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    /**
     * @param ContextGuardInterface $service
     */
    public function register($service)
    {
        $this->registryStorage->store($service, ContextGuardInterface::class,'contextGuard', get_class($service));
    }

    /**
     * {@inheritdoc}
     */
    public function getAllGuards()
    {
        return $this->registryStorage->getByNamespace('contextGuard');
    }
}
