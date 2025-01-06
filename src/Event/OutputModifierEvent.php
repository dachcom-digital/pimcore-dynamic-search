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

namespace DynamicSearchBundle\Event;

class OutputModifierEvent
{
    public function __construct(protected array $parameter)
    {
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
