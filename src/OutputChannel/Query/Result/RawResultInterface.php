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
