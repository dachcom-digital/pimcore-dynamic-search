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

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionResolverInterface
{
    /**
     * @return array<int, DocumentDefinitionBuilderInterface>
     *
     * @throws DefinitionNotFoundException
     */
    public function resolveForContext(string $contextName): array;

    /**
     * @return array<int, DocumentDefinitionBuilderInterface>
     *
     * @throws DefinitionNotFoundException
     */
    public function resolve(string $contextName, ResourceMetaInterface $resourceMeta): array;
}
