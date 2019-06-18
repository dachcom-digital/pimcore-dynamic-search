<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\DispatchTransformerNotFoundException;
use DynamicSearchBundle\Registry\TransformerRegistryInterface;
use DynamicSearchBundle\Transformer\DispatchTransformerContainer;
use DynamicSearchBundle\Transformer\DispatchTransformerContainerInterface;
use DynamicSearchBundle\Transformer\DispatchTransformerInterface;

class DispatchTransformerResolver implements DataResolverInterface
{
    /**
     * @var TransformerRegistryInterface
     */
    protected $dataTransformerRegistry;

    /**
     * @param TransformerRegistryInterface $dataTransformerRegistry
     */
    public function __construct(TransformerRegistryInterface $dataTransformerRegistry)
    {
        $this->dataTransformerRegistry = $dataTransformerRegistry;
    }

    /**
     * @param mixed $data
     *
     * @return DispatchTransformerContainerInterface
     *
     * @throws DispatchTransformerNotFoundException
     */
    public function resolve($data)
    {
        $validTransformer = null;
        $validTransformerName = null;

        foreach ($this->dataTransformerRegistry->getAllDispatchTransformers() as $dataTransformerName => $dataTransformer) {
            if ($dataTransformer->isApplicable($data) === true) {
                $validTransformer = $dataTransformer;
                $validTransformerName = $dataTransformerName;
                break;
            }
        }

        if ($validTransformer instanceof DispatchTransformerInterface) {
            return new DispatchTransformerContainer($validTransformer, $validTransformerName);
        }

        throw new DispatchTransformerNotFoundException();
    }
}