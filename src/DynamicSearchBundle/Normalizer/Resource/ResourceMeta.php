<?php

namespace DynamicSearchBundle\Normalizer\Resource;

class ResourceMeta implements ResourceMetaInterface
{
    /**
     * @var mixed
     */
    public $documentId;

    /**
     * @var mixed
     */
    public $resourceId;

    public string $resourceCollectionType;
    public string $resourceType;
    public ?string $resourceSubType;
    public array $resourceOptions;
    public array $normalizerOptions;

    public function __construct(
        $documentId,
        $resourceId,
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

    /**
     * {@inheritdoc}
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionType(): string
    {
        return $this->resourceCollectionType;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getResourceSubType(): ?string
    {
        return $this->resourceSubType;
    }

    public function getResourceId()
    {
        return $this->resourceId;
    }

    public function getResourceOptions(): array
    {
        return $this->resourceOptions;
    }

    public function hasNormalizerOption(string $option): bool
    {
        return isset($this->normalizerOptions[$option]);
    }

    public function getNormalizerOption(string $option)
    {
        return $this->normalizerOptions[$option];
    }

    public function getNormalizerOptions(): array
    {
        return is_array($this->normalizerOptions) ? $this->normalizerOptions : [];
    }
}
