<?php

namespace DynamicSearchBundle\Resource;

interface ResourceInfoInterface
{
    public const TYPE_PIMCORE_ELEMENT = 'pimcore_element';

    public function getResourceId(): int|string;
    public function getResourceType(): string;
    public function getResourceLocale(): ?string;
}
