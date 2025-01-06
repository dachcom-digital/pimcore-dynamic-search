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

namespace DynamicSearchBundle;

final class DynamicSearchEvents
{
    public const ERROR_DISPATCH_CRITICAL = 'ds.error.critical';
    public const ERROR_DISPATCH_ABORT = 'ds.error.abort';
    public const NEW_DATA_AVAILABLE = 'ds.data.new';
    public const RESOURCE_CANDIDATE_VALIDATION = 'ds.data.resource.validation';
}
