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

namespace DynamicSearchBundle;

use DynamicSearchBundle\DependencyInjection\Compiler\ContextGuardPass;
use DynamicSearchBundle\DependencyInjection\Compiler\DataProviderPass;
use DynamicSearchBundle\DependencyInjection\Compiler\DefinitionBuilderPass;
use DynamicSearchBundle\DependencyInjection\Compiler\HealthStatePass;
use DynamicSearchBundle\DependencyInjection\Compiler\IndexPass;
use DynamicSearchBundle\DependencyInjection\Compiler\IndexProviderPass;
use DynamicSearchBundle\DependencyInjection\Compiler\NormalizerPass;
use DynamicSearchBundle\DependencyInjection\Compiler\OutputChannelPass;
use DynamicSearchBundle\DependencyInjection\Compiler\ResourceTransformerPass;
use DynamicSearchBundle\Tool\Install;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DynamicSearchBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public const PACKAGE_NAME = 'dachcom-digital/dynamic-search';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new DataProviderPass());
        $container->addCompilerPass(new IndexProviderPass());
        $container->addCompilerPass(new DefinitionBuilderPass());
        $container->addCompilerPass(new NormalizerPass());
        $container->addCompilerPass(new ResourceTransformerPass());
        $container->addCompilerPass(new IndexPass());
        $container->addCompilerPass(new OutputChannelPass());
        $container->addCompilerPass(new ContextGuardPass());
        $container->addCompilerPass(new HealthStatePass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getInstaller(): Install
    {
        return $this->container->get(Install::class);
    }

    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }
}
