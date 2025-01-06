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

namespace DynamicSearchBundle\Logger;

use Monolog\LogRecord;

class ProviderContextProcessor
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $record['extra']['provider'] = $record['context']['provider'] ?? '--';
        $record['extra']['contextName'] = $record['context']['contextName'] ?? '--';

        return $record;
    }
}
