<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Resource\ResourceScaffolderInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;

class TransformerRegistry implements TransformerRegistryInterface
{
    /**
     * @var array
     */
    protected $resourceScaffolder;

    /**
     * @var array
     */
    protected $resourceFieldTransformer;

    /**
     * @param ResourceScaffolderInterface $service
     * @param string                      $identifier
     * @param string                      $dataProvider
     */
    public function registerResourceScaffolder($service, string $identifier, string $dataProvider)
    {
        if (!in_array(ResourceScaffolderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), ResourceScaffolderInterface::class, implode(', ', class_implements($service)))
            );
        }

        if (!isset($this->resourceScaffolder[$dataProvider])) {
            $this->resourceScaffolder[$dataProvider] = [];
        }

        $this->resourceScaffolder[$dataProvider][$identifier] = $service;
    }

    /**
     * @param FieldTransformerInterface $service
     * @param string                    $identifier
     * @param string                    $resourceScaffolder
     */
    public function registerResourceFieldTransformer($service, string $identifier, string $resourceScaffolder)
    {
        if (!in_array(FieldTransformerInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), FieldTransformerInterface::class, implode(', ', class_implements($service)))
            );
        }

        if (!isset($this->resourceFieldTransformer[$resourceScaffolder])) {
            $this->resourceFieldTransformer[$resourceScaffolder] = [];
        }

        $this->resourceFieldTransformer[$resourceScaffolder][$identifier] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function hasResourceFieldTransformer(string $resourceScaffolderName, string $identifier)
    {
        return isset($this->resourceFieldTransformer[$resourceScaffolderName]) && isset($this->resourceFieldTransformer[$resourceScaffolderName][$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceFieldTransformer(string $resourceScaffolderName, string $identifier)
    {
        return $this->resourceFieldTransformer[$resourceScaffolderName][$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function getAllResourceScaffolderForDataProvider(string $dataProviderName)
    {
        return isset($this->resourceScaffolder[$dataProviderName]) ? $this->resourceScaffolder[$dataProviderName] : [];
    }
}
