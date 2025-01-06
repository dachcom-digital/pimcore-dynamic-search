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

namespace DynamicSearchBundle\OutputChannel\Result;

use Knp\Component\Pager\Pagination\PaginationInterface;

class OutputChannelPaginatorResult extends OutputChannelResult implements OutputChannelPaginatorResultInterface
{
    protected PaginationInterface $paginator;

    public function setPaginator(PaginationInterface $paginator): void
    {
        $this->paginator = $paginator;
    }

    public function getPaginator(): PaginationInterface
    {
        return $this->paginator;
    }
}
