<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\DocumentTransformerNotFoundException;
use DynamicSearchBundle\Registry\TransformerRegistryInterface;
use DynamicSearchBundle\Transformer\DocumentTransformerContainer;
use DynamicSearchBundle\Transformer\DocumentTransformerInterface;

class DocumentTransformerResolver implements DocumentTransformerResolverInterface
{
    /**
     * @var TransformerRegistryInterface
     */
    protected $transformerRegistry;

    /**
     * @param TransformerRegistryInterface $transformerRegistry
     */
    public function __construct(TransformerRegistryInterface $transformerRegistry)
    {
        $this->transformerRegistry = $transformerRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($resource)
    {
        $validTransformer = null;
        $validTransformerName = null;

        foreach ($this->transformerRegistry->getAllDocumentTransformers() as $dataTransformerName => $dataTransformer) {
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