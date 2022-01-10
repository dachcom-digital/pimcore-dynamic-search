<?php

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
