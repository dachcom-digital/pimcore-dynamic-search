<?php

namespace DynamicSearchBundle;

use DynamicSearchBundle\DependencyInjection\Compiler\DataProviderPass;
use DynamicSearchBundle\DependencyInjection\Compiler\IndexFieldPass;
use DynamicSearchBundle\DependencyInjection\Compiler\IndexProviderPass;
use DynamicSearchBundle\DependencyInjection\Compiler\TransformerPass;
use DynamicSearchBundle\Tool\Install;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DynamicSearchBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    const PACKAGE_NAME = 'dachcom-digital/dynamic-search';

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DataProviderPass());
        $container->addCompilerPass(new IndexProviderPass());
        $container->addCompilerPass(new TransformerPass());
        $container->addCompilerPass(new IndexFieldPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        return $this->container->get(Install::class);
    }

    /**
     * @return string[]
     */
    public function getJsPaths()
    {
        return [
            '/bundles/dynamicsearch/js/plugin.js',
        ];
    }

    /**
     * @return array
     */
    public function getCssPaths()
    {
        return [
            '/bundles/dynamicsearch/css/admin.css'
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }
}
