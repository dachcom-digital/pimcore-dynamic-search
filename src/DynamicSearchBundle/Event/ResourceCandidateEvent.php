<?php

namespace DynamicSearchBundle\Event;

use DynamicSearchBundle\Resource\ResourceCandidateInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ResourceCandidateEvent extends Event
{
    /**
     * @var ResourceCandidateInterface
     */
    protected $resourceCandidate;

    /**
     * @param ResourceCandidateInterface $resourceCandidate
     */
    public function __construct(ResourceCandidateInterface $resourceCandidate)
    {
        $this->resourceCandidate = $resourceCandidate;
    }

    /**
     * @param ResourceCandidateInterface $resourceCandidate
     */
    public function setResourceCandidate(ResourceCandidateInterface $resourceCandidate)
    {
        $this->resourceCandidate = $resourceCandidate;
    }

    /**
     * @return ResourceCandidateInterface
     */
    public function getResourceCandidate()
    {
        return $this->resourceCandidate;
    }
}
