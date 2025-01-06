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

namespace DynamicSearchBundle\OutputChannel;

use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\Query\SearchContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface OutputChannelInterface
{
    public static function configureOptions(OptionsResolver $resolver): void;

    public function setOptions(array $options): void;

    public function setOutputChannelContext(OutputChannelContextInterface $outputChannelContext): void;

    public function setEventDispatcher(OutputChannelModifierEventDispatcher $eventDispatcher): void;

    /**
     * @throws \Exception
     */
    public function getQuery(): mixed;

    /**
     * @throws \Exception
     */
    public function getResult(SearchContainerInterface $searchContainer): SearchContainerInterface;
}
