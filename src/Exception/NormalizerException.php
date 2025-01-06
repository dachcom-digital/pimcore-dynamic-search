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

final class NormalizerException extends \Exception
{
    public function __construct(string $message, ?string $normalizerName = null, ?\Throwable $previousException = null)
    {
        $normalizerName = is_null($normalizerName) ? '' : sprintf(' (%s)', $normalizerName);

        parent::__construct(sprintf('Normalizer Error%s: %s', $normalizerName, $message), 0, $previousException);
    }
}
