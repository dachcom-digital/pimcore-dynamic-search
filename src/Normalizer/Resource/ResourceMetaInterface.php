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
