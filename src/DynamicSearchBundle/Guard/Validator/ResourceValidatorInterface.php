<?php

namespace DynamicSearchBundle\Guard\Validator;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceValidatorInterface
{
    public function validate(string $contextName, string $dispatchType, ResourceMetaInterface $resourceMeta, $resource);
}
