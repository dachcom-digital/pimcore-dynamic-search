<?php

namespace DynamicSearchBundle\Normalizer\Resource;

interface ResourceMetaInterface
{
    /**
     * @return string|int
     */
    public function getDocumentId();

    public function getResourceCollectionType(): string;

    public function getResourceType(): string;

    public function getResourceSubType(): ?string;

    /**
     * @return mixed
     */
    public function getResourceId();

    public function getResourceOptions(): array;

    public function hasNormalizerOption(string $option): bool;

    /**
     * @return mixed
     */
    public function getNormalizerOption(string $option);

    public function getNormalizerOptions(): array;
}
