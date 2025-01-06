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

final class OutputChannelException extends \Exception
{
    public function __construct(string $type, string $message, ?\Throwable $previousException = null)
    {
        parent::__construct(sprintf('Output Channel "%s" Exception: %s', $type, $message), 0, $previousException);
    }
}
