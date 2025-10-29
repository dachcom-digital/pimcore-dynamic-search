<?php

namespace DynamicSearchBundle\Resource;

class ResourceInfo implements ResourceInfoInterface
{
    public function __construct(
        protected int|string $resourceId,
        protected string $resourceType,
        protected ?string $resourceLocale = null
    )
    {
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getResourceId(): int|string
    {
        return $this->resourceId;
    }

    public function getResourceLocale(): string
    {
        return $this->resourceLocale;
    }

    public function setResourceType(string $resourceType): void
    {
        $this->resourceType = $resourceType;
    }

    public function setResourceId(int|string $resourceId): void
    {
        $this->resourceId = $resourceId;
    }

    public function setResourceLocale(string $resourceLocale): void
    {
        $this->resourceLocale = $resourceLocale;
    }

}
