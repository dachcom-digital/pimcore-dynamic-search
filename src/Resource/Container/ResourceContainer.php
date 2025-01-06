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

class ResourceContainer implements ResourceContainerInterface
{
    public function __construct(
        protected mixed $resource,
        protected bool $isBaseResource,
        protected string $resourceScaffolderIdentifier,
        protected array $attributes = []
    ) {
    }

    public function hasResource(): bool
    {
        return $this->resource !== null;
    }

    public function isBaseResource(): bool
    {
        return $this->isBaseResource === true;
    }

    public function getResource(): mixed
    {
        return $this->resource;
    }

    public function getResourceScaffolderIdentifier(): string
    {
        return $this->resourceScaffolderIdentifier;
    }

    public function hasAttribute(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    public function getAttribute(string $attribute): mixed
    {
        return $this->attributes[$attribute];
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
