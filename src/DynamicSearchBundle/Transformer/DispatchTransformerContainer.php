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

    public function __construct(DispatchTransformerInterface $transformer, string $identifier)
    {
        $this->transformer = $transformer;
        $this->identifier = $identifier;
    }

    public function getTransformer()
    {
        return $this->transformer;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }
}
