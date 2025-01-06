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
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\ProviderException;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface IndexProviderInterface extends ProviderInterface
{
    public static function configureOptions(OptionsResolver $resolver): void;

    /**
     * @throws ProviderException
     */
    public function processDocument(ContextDefinitionInterface $contextDefinition, IndexDocument $indexDocument): void;
}
