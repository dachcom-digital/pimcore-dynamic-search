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

use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

class NormalizedDataResource implements NormalizedDataResourceInterface
{
    public function __construct(
        protected ?ResourceContainerInterface $resourceContainer,
        protected ResourceMetaInterface $resourceMeta
    ) {
    }

    public function getResourceContainer(): ?ResourceContainerInterface
    {
        return $this->resourceContainer;
    }

    public function getResourceMeta(): ResourceMetaInterface
    {
        return $this->resourceMeta;
    }
}
