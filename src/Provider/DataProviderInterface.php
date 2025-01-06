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

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface DataProviderInterface extends ProviderInterface
{
    public const PROVIDER_BEHAVIOUR_FULL_DISPATCH = 'full_dispatch';
    public const PROVIDER_BEHAVIOUR_SINGLE_DISPATCH = 'single_dispatch';

    public static function configureOptions(OptionsResolver $resolver): void;

    /**
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function provideAll(ContextDefinitionInterface $contextDefinition): void;

    /**
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function provideSingle(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta): void;
}
