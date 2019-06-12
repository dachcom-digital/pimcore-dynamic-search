<?php

namespace DynamicSearchBundle\Exception;

final class ContextConfigurationException extends \Exception
{
    public function __construct($type, $message)
    {
        parent::__construct(sprintf('Error while asserting context configuration for "%s". Validation Message was: %s', $type, $message));
    }
}
