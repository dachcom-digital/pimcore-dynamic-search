<?php

namespace DynamicSearchBundle\Document\Definition;

interface DocumentDefinitionContextBuilderInterface
{
    public function isApplicableForContext(string $contextName): bool;
}
