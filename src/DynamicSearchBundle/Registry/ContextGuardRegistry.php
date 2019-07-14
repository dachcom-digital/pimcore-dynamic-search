<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Guard\ContextGuardInterface;

class ContextGuardRegistry implements ContextGuardRegistryInterface
{
    /**
     * @var array
     */
    protected $guards;

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

        $this->guards[] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllGuards()
    {
        return !is_array($this->guards) ? [] : $this->guards;
    }
}
