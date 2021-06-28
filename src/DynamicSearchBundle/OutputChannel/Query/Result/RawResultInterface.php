<?php

namespace DynamicSearchBundle\OutputChannel\Query\Result;

interface RawResultInterface
{
    /**
     * @param mixed $data
     */
    public function setData($data): void;

    /**
     * @return mixed
     */
    public function getData();

    public function setHitCount(int $hitCount): void;

    public function getHitCount(): int;

    public function addParameter(string $key, $value): void;

    public function setParameter(string $key, $value): void;

    public function hasParameter(string $key): bool;

    public function getParameter(string $key);
}
