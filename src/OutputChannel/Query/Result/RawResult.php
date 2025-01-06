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

namespace DynamicSearchBundle\OutputChannel\Query\Result;

class RawResult implements RawResultInterface
{
    protected mixed $data;
    protected int $hitCount;
    protected array $parameter;

    public function __construct()
    {
        $this->hitCount = 0;
        $this->parameter = [];
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    public function getData(): mixed
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

    public function addParameter(string $key, mixed $value): void
    {
        $this->setParameter($key, $value);
    }

    public function setParameter(string $key, mixed $value): void
    {
        $this->parameter[$key] = $value;
    }

    public function hasParameter(string $key): bool
    {
        return isset($this->parameter[$key]);
    }

    public function getParameter(string $key): mixed
    {
        return $this->parameter[$key];
    }
}
