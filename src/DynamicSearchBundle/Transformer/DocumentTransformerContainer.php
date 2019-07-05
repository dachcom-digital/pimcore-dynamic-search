<?php

namespace DynamicSearchBundle\Transformer;

class DocumentTransformerContainer implements DocumentTransformerContainerInterface
{
    /**
     * @var DocumentTransformerInterface
     */
    protected $transformer;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @param DocumentTransformerInterface $transformer
     * @param string                       $identifier
     */
    public function __construct(DocumentTransformerInterface $transformer, string $identifier)
    {
        $this->transformer = $transformer;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
