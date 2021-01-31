<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Registry\Storage\RegistryStorage;
use DynamicSearchBundle\Resource\ResourceScaffolderInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;

class TransformerRegistry implements TransformerRegistryInterface
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
     * @param ResourceScaffolderInterface $service
     * @param string                      $identifier
     * @param string|null                 $alias
     * @param string                      $dataProvider
     */
    public function registerResourceScaffolder($service, string $identifier, ?string $alias, string $dataProvider)
    {
        if (!in_array(ResourceScaffolderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), ResourceScaffolderInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->registryStorage->store($service, $dataProvider, $identifier, $alias);
    }

    /**
     * @param FieldTransformerInterface $service
     * @param string                    $identifier
     * @param string|null               $alias
     * @param string                    $resourceScaffolder
     */
    public function registerResourceFieldTransformer($service, string $identifier, ?string $alias, string $resourceScaffolder)
    {
        if (!in_array(FieldTransformerInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), FieldTransformerInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->registryStorage->store($service, $resourceScaffolder, $identifier, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function hasResourceFieldTransformer(string $resourceScaffolderName, string $identifier)
    {
        return $this->registryStorage->has($resourceScaffolderName, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceFieldTransformer(string $resourceScaffolderName, string $identifier)
    {
        return $this->registryStorage->get($resourceScaffolderName, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllResourceScaffolderForDataProvider(string $dataProviderName)
    {
        return $this->registryStorage->getByNamespace($dataProviderName);
    }
}
