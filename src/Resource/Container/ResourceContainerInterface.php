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

namespace DynamicSearchBundle\Resource\Container;

interface ResourceContainerInterface
{
    public function hasResource(): bool;

    public function isBaseResource(): bool;

    public function getResource(): mixed;

    public function getResourceScaffolderIdentifier(): string;

    public function hasAttribute(string $attribute): bool;

    public function getAttribute(string $attribute): mixed;

    public function getAttributes(): array;
}
