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

namespace DynamicSearchBundle\Service;

use Doctrine\DBAL\Connection;
use DynamicSearchBundle\Doctrine\DBAL\ConnectionKeepAlive;

class LongProcessService implements LongProcessServiceInterface
{
    protected Connection $connection;
    protected ConnectionKeepAlive $keepAlive;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function boot(): void
    {
        $this->keepAlive = new ConnectionKeepAlive();
        $this->keepAlive->addConnection($this->connection);
        $this->keepAlive->attach();
    }

    public function shutdown(): void
    {
        $this->keepAlive->detach();
    }
}
