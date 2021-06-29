<?php

namespace DynamicSearchBundle\OutputChannel\Query\Result;

class RawResult implements RawResultInterface
{
    /**
     * @var mixed
     */
    protected $data;

    protected int $hitCount;
    protected array $parameter;

    public function __construct()
    {
        $this->hitCount = 0;
        $this->parameter = [];
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setHitCount(int $hitCount): void
    {
        $this->hitCount = $hitCount;
    }

    public function getHitCount(): int
    {
        return $this->hitCount;
    }

    public function addParameter($key, $value): void
    {
        $this->setParameter($key, $value);
    }

    public function setParameter($key, $value): void
    {
        $this->parameter[$key] = $value;
    }

    public function hasParameter($key): bool
    {
        return isset($this->parameter[$key]);
    }

    public function getParameter($key)
    {
        return $this->parameter[$key];
    }
}
