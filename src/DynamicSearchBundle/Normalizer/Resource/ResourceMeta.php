<?php

namespace DynamicSearchBundle\Normalizer\Resource;

class ResourceMeta implements ResourceMetaInterface
{
    public string|int $documentId;
    public string|int $resourceId;
    public string $resourceCollectionType;
    public string $resourceType;
    public ?string $resourceSubType;
    public array $resourceOptions;
    public array $normalizerOptions;

    public function __construct(
        string|int $documentId,
        string|int $resourceId,
        string $resourceCollectionType,
        string $resourceType,
        ?string $resourceSubType,
        array $resourceOptions = [],
        array $normalizerOptions = []
    ) {
        $this->documentId = $documentId;
        $this->resourceId = $resourceId;
        $this->resourceCollectionType = $resourceCollectionType;
        $this->resourceType = $resourceType;
        $this->resourceSubType = $resourceSubType;
        $this->resourceOptions = $resourceOptions;
        $this->normalizerOptions = $normalizerOptions;
    }

    public function getDocumentId(): string|int
    {
        return $this->documentId;
    }

    public function getResourceId(): string|int
    {
        return $this->resourceId;
    }

    public function getResourceCollectionType(): string
    {
        return $this->resourceCollectionType;
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getResourceSubType(): ?string
    {
        return $this->resourceSubType;
    }

    public function getResourceOptions(): array
    {
        return $this->resourceOptions;
    }

    public function hasNormalizerOption(string $option): bool
    {
        return isset($this->normalizerOptions[$option]);
    }

    public function getNormalizerOption(string $option): mixed
    {
        return $this->normalizerOptions[$option];
    }

    public function getNormalizerOptions(): array
    {
        return $this->normalizerOptions;
    }
}
