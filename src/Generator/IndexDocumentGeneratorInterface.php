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

namespace DynamicSearchBundle\Generator;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

interface IndexDocumentGeneratorInterface
{
    /**
     * @throws \Exception
     * @throws SilentException
     */
    public function generate(
        ContextDefinitionInterface $contextDefinition,
        ResourceMetaInterface $resourceMeta,
        ResourceContainerInterface $resourceContainer,
        array $options = []
    ): IndexDocument;

    /**
     * @throws \Exception
     * @throws SilentException
     */
    public function generateWithoutData(
        ContextDefinitionInterface $contextDefinition,
        array $options = []
    ): IndexDocument;
}
