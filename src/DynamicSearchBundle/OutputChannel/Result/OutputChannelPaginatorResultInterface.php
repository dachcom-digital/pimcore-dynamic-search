<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\Paginator\PaginatorInterface;

interface OutputChannelPaginatorResultInterface
{
    public function getPaginator(): ?PaginatorInterface;
}
