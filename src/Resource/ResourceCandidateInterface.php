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

namespace DynamicSearchBundle\Resource;

interface ResourceCandidateInterface
{
    public function isAllowedToModifyDispatchType(): bool;

    public function isAllowedToModifyResource(): bool;

    /**
     * @throws \Exception
     */
    public function setResource($resource): void;

    public function getResource(): mixed;

    /**
     * @throws \Exception
     */
    public function setDispatchType(string $dispatchType): void;

    public function getDispatchType(): string;
}
