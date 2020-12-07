<?php

namespace DynamicSearchBundle\Document\Definition;

interface DocumentDefinitionContextBuilderInterface
{
    /**
     * @param string $contextName
     *
     * @return bool
     */
    public function isApplicableForContext(string $contextName);
}
