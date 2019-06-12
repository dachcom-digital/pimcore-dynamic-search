<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Transformer\DataTransformerInterface;

class DataTransformerRegistry implements DataTransformerRegistryInterface
{
    /**
     * @var array
     */
    protected $transformer;

    /**
     * @param $service
     */
    public function register($service)
    {
        if (!in_array(DataTransformerInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), DataTransformerInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->transformer[] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->transformer;
    }

}