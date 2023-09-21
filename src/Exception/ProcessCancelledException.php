<?php

namespace DynamicSearchBundle\Exception;

final class ProcessCancelledException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
