<?php

namespace DynamicSearchBundle\OutputChannel\Query\Result;

interface RawResultInterface
{
    public function setData(mixed $data): void;

    public function getData(): mixed;

    public function setHitCount(int $hitCount): void;

    public function getHitCount(): int;

    public function addParameter(string $key, mixed $value): void;

    public function setParameter(string $key, mixed $value): void;

    public function hasParameter(string $key): bool;

    public function getParameter(string $key): mixed;
}
