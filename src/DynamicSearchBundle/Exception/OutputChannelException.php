<?php

namespace DynamicSearchBundle\Exception;

final class OutputChannelException extends \Exception
{
    /**
     * @param string          $type
     * @param string          $message
     * @param \Exception|null $previousException
     */
    public function __construct($type, $message, $previousException = null)
    {
        parent::__construct(sprintf('Output Channel "%s" Exception: %s', $type, $message), 0, $previousException);
    }
}
