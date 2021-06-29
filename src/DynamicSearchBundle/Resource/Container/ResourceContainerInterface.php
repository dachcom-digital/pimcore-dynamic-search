<?php

namespace DynamicSearchBundle\Resource\Container;

interface ResourceContainerInterface
{
    public function hasResource(): bool;

    public function isBaseResource(): bool;

    public function getResource();

    public function getResourceScaffolderIdentifier(): string;

    public function hasAttribute(string $attribute): bool;

    public function getAttribute(string $attribute);

    public function getAttributes(): array;
}
