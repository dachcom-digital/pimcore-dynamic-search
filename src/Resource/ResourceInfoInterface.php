<?php

namespace DynamicSearchBundle\Resource;

interface ResourceInfoInterface
{
    public function getResourceId(): int|string;
    public function getResourceType(): string;
    public function getResourceLocale(): ?string;
}
