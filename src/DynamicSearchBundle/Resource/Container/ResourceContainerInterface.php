<?php

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
