<?php

namespace DynamicSearchBundle\Exception;

final class DataTransformerException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct(sprintf('Error while executing data transformer. Message was: %s', $message));
    }
}
