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
        if (!in_array(ContextGuardInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s needs to implement "%s", "%s" given.',
                    get_class($service),
                    ContextGuardInterface::class,
                    implode(', ', class_implements($service))
                )
            );
        }

        $this->registryStorage->store($service, 'contextGuard', get_class($service));

    }

    /**
     * {@inheritdoc}
     */
    public function getAllGuards()
    {
        return $this->registryStorage->getByNamespace('contextGuard');
    }
}
