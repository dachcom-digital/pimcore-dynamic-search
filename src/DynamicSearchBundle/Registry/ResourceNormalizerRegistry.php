<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class ResourceNormalizerRegistry implements ResourceNormalizerRegistryInterface
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
     * @param ResourceNormalizerInterface $service
     * @param string                      $identifier
     * @param string|null                 $alias
     * @param string                      $dataProviderName
     */
    public function registerResourceNormalizer($service, string $identifier, ?string $alias, string $dataProviderName)
    {
        if (!in_array(ResourceNormalizerInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), ResourceNormalizerInterface::class, implode(', ', class_implements($service)))
            );
        }

        $namespace = sprintf('resourceNormalizer_%s', $dataProviderName);
        $this->registryStorage->store($service, $namespace, $identifier, $alias);
    }

    /**
     * @param DocumentNormalizerInterface $service
     * @param string                      $identifier
     * @param string|null                 $alias
     * @param string                      $indexProviderName
     */
    public function registerDocumentNormalizer($service, string $identifier, ?string $alias, string $indexProviderName)
    {
        if (!in_array(DocumentNormalizerInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), DocumentNormalizerInterface::class, implode(', ', class_implements($service)))
            );
        }

        $namespace = sprintf('documentNormalizer_%s', $indexProviderName);
        $this->registryStorage->store($service, $namespace, $identifier, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceNormalizerForDataProvider(string $dataProviderName, string $identifier)
    {
        $namespace = sprintf('resourceNormalizer_%s', $dataProviderName);

        return $this->registryStorage->get($namespace, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function hasResourceNormalizerForDataProvider(string $dataProviderName, string $identifier)
    {
        $namespace = sprintf('resourceNormalizer_%s', $dataProviderName);

        return $this->registryStorage->has($namespace, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier)
    {
        $namespace = sprintf('documentNormalizer_%s', $indexProviderName);

        return $this->registryStorage->get($namespace, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function hasDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier)
    {
        $namespace = sprintf('documentNormalizer_%s', $indexProviderName);

        return $this->registryStorage->has($namespace, $identifier);
    }
}
