<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Resource\ResourceCandidateInterface;

interface DataProviderValidationAwareInterface extends ProviderInterface
{
    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param ResourceCandidateInterface  $resourceCandidate
     */
    public function validateResource(ContextDefinitionInterface $contextDefinition, ResourceCandidateInterface $resourceCandidate);
}
