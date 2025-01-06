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

namespace DynamicSearchBundle\Paginator;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;

interface AdapterInterface
{
    public function setContextDefinition(ContextDefinitionInterface $contextDefinition): void;

    public function setOutputChannelName(string $outputChannelName): void;

    public function setDocumentNormalizer(?DocumentNormalizerInterface $documentNormalizer): void;

    public function setItemCountPerPage(int $itemCountPerPage): void;

    public function setCurrentPageNumber(int $currentPageNumber): void;

    public function getItems(int $offset, int $itemCountPerPage): array;

    public function getCount(): int;
}
