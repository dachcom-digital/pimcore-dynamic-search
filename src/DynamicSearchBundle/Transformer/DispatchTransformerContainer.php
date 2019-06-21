<?php

namespace DynamicSearchBundle\Transformer;

class DispatchTransformerContainer implements DispatchTransformerContainerInterface
{

    /**
     * @var DispatchTransformerInterface
     */
    protected $transformer;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @param DispatchTransformerInterface $transformer
     * @param string                       $identifier
     */
    public function __construct(DispatchTransformerInterface $transformer, string $identifier)
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
