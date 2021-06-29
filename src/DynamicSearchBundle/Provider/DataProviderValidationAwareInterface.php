<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Resource\ResourceCandidateInterface;

interface DataProviderValidationAwareInterface extends ProviderInterface
{
    public function validateResource(ContextDefinitionInterface $contextDefinition, ResourceCandidateInterface $resourceCandidate): ResourceCandidateInterface;
}
