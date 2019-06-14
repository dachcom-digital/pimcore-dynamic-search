<?php

namespace DynamicSearchBundle\Exception;

final class ProviderException extends \Exception
{
    /**
     * @param string          $message
     * @param string|null     $providerName
     * @param \Exception|null $previousException
     */
    public function __construct(string $message, string $providerName = null, \Exception $previousException = null)
    {
        $providerName = is_null($providerName) ? '' : sprintf(' (%s)', $providerName);

        parent::__construct(sprintf('Provider Error%s: %s', $providerName, $message), 0, $previousException);
    }
}
