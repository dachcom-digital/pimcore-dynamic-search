<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\DocumentTransformerNotFoundException;
use DynamicSearchBundle\Registry\TransformerRegistryInterface;
use DynamicSearchBundle\Transformer\DocumentTransformerContainer;
use DynamicSearchBundle\Transformer\DocumentTransformerInterface;

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
     * {@inheritDoc}
     */
    public function resolve($resource)
    {
        $validTransformer = null;
        $validTransformerName = null;

        foreach ($this->dataTransformerRegistry->getAllDispatchTransformers() as $dataTransformerName => $dataTransformer) {
            if ($dataTransformer->isApplicable($resource) === true) {
                $validTransformer = $dataTransformer;
                $validTransformerName = $dataTransformerName;
                break;
            }
        }

        if ($validTransformer instanceof DocumentTransformerInterface) {
            return new DocumentTransformerContainer($validTransformer, $validTransformerName);
        }

        throw new DocumentTransformerNotFoundException();
    }
}