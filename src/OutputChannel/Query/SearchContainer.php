<?php

namespace DynamicSearchBundle\OutputChannel\Query;

use DynamicSearchBundle\OutputChannel\Query\Result\RawResult;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;

class SearchContainer implements SearchContainerInterface
{
    public RawResultInterface $result;
    protected string $identifier;
    protected mixed $query;

    public function __construct(string $identifier, mixed $query)
    {
        $this->result = new RawResult();
        $this->identifier = $identifier;
        $this->query = $query;
    }

    public function getQuery(): mixed
    {
        return $this->query;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getRawResult(): RawResultInterface
    {
        return $this->result;
    }
}
