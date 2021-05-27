<?php

namespace DynamicSearchBundle\Resource;

class ResourceCandidate implements ResourceCandidateInterface
{
    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var string
     */
    protected $dispatchType;

    /**
     * @var bool
     */
    protected $allowDispatchTypeModification;

    /**
     * @var bool
     */
    protected $allowResourceModification;

    /**
     * @var mixed
     */
    protected $resource;

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param bool   $allowDispatchTypeModification
     * @param bool   $allowResourceModification
     * @param mixed  $resource
     */
    public function __construct(string $contextName, string $dispatchType, bool $allowDispatchTypeModification, bool $allowResourceModification, $resource)
    {
        $this->contextName = $contextName;
        $this->dispatchType = $dispatchType;
        $this->allowDispatchTypeModification = $allowDispatchTypeModification;
        $this->allowResourceModification = $allowResourceModification;
        $this->resource = $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function isAllowedToModifyDispatchType()
    {
        return $this->allowDispatchTypeModification === true;
    }

    /**
     * {@inheritDoc}
     */
    public function isAllowedToModifyResource()
    {
        return $this->allowResourceModification === true;
    }

    /**
     * {@inheritDoc}
     */
    public function setResource($resource)
    {
        if ($this->allowResourceModification === false && $resource !== null) {
            throw new \Exception(
                sprintf(
                    'Only resource deletion (null) is allowed at current state (%s)',
                    $this->dispatchType
                )
            );
        }

        $this->resource = $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritDoc}
     */
    public function setDispatchType(string $dispatchType)
    {
        if ($this->allowDispatchTypeModification === false) {
            throw new \Exception(
                sprintf(
                    'Changing dispatch type from "%s" to "%s" is not allowed at current state',
                    $this->dispatchType, $dispatchType
                )
            );
        }

        $this->dispatchType = $dispatchType;
    }

    /**
     * {@inheritDoc}
     */
    public function getDispatchType()
    {
        return $this->dispatchType;
    }
}
