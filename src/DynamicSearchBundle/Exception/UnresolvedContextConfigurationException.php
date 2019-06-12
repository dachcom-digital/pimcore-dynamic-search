<?php

namespace DynamicSearchBundle\Exception;

final class UnresolvedContextConfigurationException extends \Exception
{
    public function __construct($type = null)
    {
        parent::__construct(sprintf('Context Configuration for "%s" has not been resolved yet. You need to execute "assertValidContextProviderOptions" or "assertValidContextTransformerOptions" before.', $type));
    }
}
