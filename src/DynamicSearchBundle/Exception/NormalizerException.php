<?php

namespace DynamicSearchBundle\Exception;

final class NormalizerException extends \Exception
{
    /**
     * @param string          $message
     * @param string|null     $normalizerName
     * @param \Exception|null $previousException
     */
    public function __construct(string $message, string $normalizerName = null, $previousException = null)
    {
        $normalizerName = is_null($normalizerName) ? '' : sprintf(' (%s)', $normalizerName);

        parent::__construct(sprintf('Normalizer Error%s: %s', $normalizerName, $message), 0, $previousException);
    }
}
