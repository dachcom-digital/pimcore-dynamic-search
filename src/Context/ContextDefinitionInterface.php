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

namespace DynamicSearchBundle\Context;

use DynamicSearchBundle\Exception\ContextConfigurationException;

interface ContextDefinitionInterface
{
    /*
     * Index: Complete data indexing
     * (full context indexing for example)
     */
    public const CONTEXT_DISPATCH_TYPE_INDEX = 'index';

    /*
     * Insert: Add single resource to index
     */
    public const CONTEXT_DISPATCH_TYPE_INSERT = 'insert';

    /*
     * Update: Update single resource in index
     */
    public const CONTEXT_DISPATCH_TYPE_UPDATE = 'update';

    /*
     * Delete: Remove single resource from index
     */
    public const CONTEXT_DISPATCH_TYPE_DELETE = 'delete';

    /*
     * Load data from index
     * and pass query data to output channel
     */
    public const CONTEXT_DISPATCH_TYPE_FETCH = 'fetch';

    /*
     * Allowed dispatch types for queue
     */
    public const ALLOWED_QUEUE_DISPATCH_TYPES = ['index', 'insert', 'update', 'delete'];

    public function getName(): string;

    public function getContextDispatchType(): string;

    public function updateRuntimeValue(string $key, mixed $value): void;

    public function getRuntimeValues(): array;

    public function getDataProviderOptions(string $providerBehaviour): array;

    public function getIndexProviderOptions(): array;

    public function getResourceNormalizerOptions(): array;

    public function getOutputChannelDocumentNormalizerOptions(string $outputChannelName): array;

    /**
     * @throws ContextConfigurationException
     */
    public function getOutputChannelOptions(string $outputChannelName): array;

    public function getOutputChannelPaginatorOptions(string $outputChannelName): array;

    public function getDataProviderName(): ?string;

    public function getIndexProviderName(): ?string;

    public function getResourceNormalizerName(): ?string;

    public function getOutputChannelNormalizerName(string $outputChannelName): ?string;

    public function getOutputChannelServiceName(string $outputChannelName): ?string;

    public function getOutputChannelEnvironment(string $outputChannelName): array;

    public function getOutputChannelRuntimeQueryProvider(string $outputChannelName): ?string;

    public function getOutputChannelRuntimeOptionsBuilder(string $outputChannelName): ?string;
}
