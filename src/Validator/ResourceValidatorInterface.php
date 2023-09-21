<?php

namespace DynamicSearchBundle\Validator;

use DynamicSearchBundle\Resource\ResourceCandidateInterface;

interface ResourceValidatorInterface
{
    public function validateResource(string $contextName, string $dispatchType, bool $isUnknownResource, bool $isImmutableResource, mixed $resource): ResourceCandidateInterface;
}
