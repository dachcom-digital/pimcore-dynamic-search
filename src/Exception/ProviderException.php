<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace DynamicSearchBundle\Exception;

final class ProviderException extends \Exception
{
    public function __construct(string $message, ?string $providerName = null, ?\Throwable $previousException = null)
    {
        $providerName = is_null($providerName) ? '' : sprintf(' (%s)', $providerName);

        parent::__construct(sprintf('Provider Error %s: %s', $providerName, $message), 0, $previousException);
    }
}
