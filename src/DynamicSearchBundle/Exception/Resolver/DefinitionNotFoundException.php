<?php

namespace DynamicSearchBundle\Exception\Resolver;

final class DefinitionNotFoundException extends \RuntimeException
{
    public function __construct(string $type, ?string $message = null)
    {
        parent::__construct($message ?: sprintf('No valid %s definition builder have been found to apply', $type));
    }
}
