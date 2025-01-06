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

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class DefinitionBuilderRegistry implements DefinitionBuilderRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function registerDocumentDefinition(DocumentDefinitionBuilderInterface $service): void
    {
        $this->registryStorage->store($service, DocumentDefinitionBuilderInterface::class, 'documentDefinitionBuilder', get_class($service));
    }

    public function registerFilterDefinition(FilterDefinitionBuilderInterface $service): void
    {
        $this->registryStorage->store($service, FilterDefinitionBuilderInterface::class, 'filterDefinitionBuilder', get_class($service));
    }

    public function getAllDocumentDefinitionBuilder(): array
    {
        return $this->registryStorage->getByNamespace('documentDefinitionBuilder');
    }

    public function getAllFilterDefinitionBuilder(): array
    {
        return $this->registryStorage->getByNamespace('filterDefinitionBuilder');
    }
}
