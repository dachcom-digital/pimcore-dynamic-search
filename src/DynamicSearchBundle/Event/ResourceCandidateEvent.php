<?php

namespace DynamicSearchBundle\Event;

use DynamicSearchBundle\Resource\ResourceCandidateInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ResourceCandidateEvent extends Event
{
    protected ResourceCandidateInterface $resourceCandidate;

    public function __construct(ResourceCandidateInterface $resourceCandidate)
    {
        $this->resourceCandidate = $resourceCandidate;
    }

    public function setResourceCandidate(ResourceCandidateInterface $resourceCandidate): void
    {
        $this->resourceCandidate = $resourceCandidate;
    }

    public function getResourceCandidate(): ResourceCandidateInterface
    {
        return $this->resourceCandidate;
    }
}
