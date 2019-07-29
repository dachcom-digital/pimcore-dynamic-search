<?php

namespace DynamicSearchBundle\Exception\Resolver;

final class DefinitionNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $type, $message = null)
    {
        parent::__construct($message ?: sprintf('No valid %s definition builder have been found to apply', $type));
    }
}
