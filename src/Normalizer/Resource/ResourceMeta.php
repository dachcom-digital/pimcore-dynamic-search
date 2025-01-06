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

namespace DynamicSearchBundle\Normalizer\Resource;

class ResourceMeta implements ResourceMetaInterface
{
    public function __construct(
        public string|int $documentId,
        public string|int $resourceId,
        public string $resourceCollectionType,
        public string $resourceType,
        public ?string $resourceSubType,
        public array $resourceOptions = [],
        public array $normalizerOptions = []
    ) {
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
