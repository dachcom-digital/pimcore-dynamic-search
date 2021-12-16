<?php

namespace DynamicSearchBundle;

use DynamicSearchBundle\DependencyInjection\Compiler\ContextGuardPass;
use DynamicSearchBundle\DependencyInjection\Compiler\DataProviderPass;
use DynamicSearchBundle\DependencyInjection\Compiler\DefinitionBuilderPass;
use DynamicSearchBundle\DependencyInjection\Compiler\HealthStatePass;
use DynamicSearchBundle\DependencyInjection\Compiler\IndexPass;
use DynamicSearchBundle\DependencyInjection\Compiler\IndexProviderPass;
use DynamicSearchBundle\DependencyInjection\Compiler\OutputChannelPass;
use DynamicSearchBundle\DependencyInjection\Compiler\NormalizerPass;
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

    public function getInstaller(): Install
    {
        return $this->container->get(Install::class);
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/dynamicsearch/js/backend/startup.js',
            '/bundles/dynamicsearch/js/backend/settings.js',
        ];
    }

    public function getCssPaths(): array
    {
        return [
            '/bundles/dynamicsearch/css/admin.css'
        ];
    }

    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }
}
