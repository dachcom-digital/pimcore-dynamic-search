<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\DataTransformerNotFoundException;
use DynamicSearchBundle\Registry\DataTransformerRegistryInterface;
use DynamicSearchBundle\Transformer\DataTransformerInterface;

class DataTransformerResolver implements DataResolverInterface
{
    /**
     * @var DataTransformerRegistryInterface
     */
    protected $dataTransformerRegistry;

    /**
     * @param DataTransformerRegistryInterface $dataTransformerRegistry
     */
    public function __construct(DataTransformerRegistryInterface $dataTransformerRegistry)
    {
        $this->dataTransformerRegistry = $dataTransformerRegistry;
    }

    /**
     * @param mixed $data
     *
     * @return DataTransformerInterface
     *
     * @throws DataTransformerNotFoundException
     */
    public function resolve($data)
    {
        $validTransformer = null;

        foreach ($this->dataTransformerRegistry->all() as $dataTransformer) {
            if ($dataTransformer->isApplicable($data) === true) {
                $validTransformer = $dataTransformer;
                break;
            }
        }

        if ($validTransformer instanceof DataTransformerInterface) {
            return $validTransformer;
        }

        throw new DataTransformerNotFoundException(null);
    }
}