<?php

namespace DynamicSearchBundle\Normalizer\Resource;

interface ResourceMetaInterface
{
    public function getDocumentId(): string|int;

    public function getResourceId(): string|int;

    public function getResourceCollectionType(): string;

    public function getResourceType(): string;

    public function getResourceSubType(): ?string;

    public function getResourceOptions(): array;

    public function hasNormalizerOption(string $option): bool;

    public function getNormalizerOption(string $option): mixed;

    public function getNormalizerOptions(): array;
}
