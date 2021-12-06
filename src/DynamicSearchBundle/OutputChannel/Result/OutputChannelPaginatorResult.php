<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\Paginator\PaginatorInterface;

class OutputChannelPaginatorResult extends OutputChannelResult implements OutputChannelPaginatorResultInterface
{
    protected PaginatorInterface $paginator;

    public function setPaginator(PaginatorInterface $paginator): void
    {
        $this->paginator = $paginator;
    }

    public function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }
}
