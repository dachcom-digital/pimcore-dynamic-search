<?php

namespace DynamicSearchBundle;

use DynamicSearchBundle\DependencyInjection\Compiler\ContextGuardPass;
use DynamicSearchBundle\DependencyInjection\Compiler\DataProviderPass;
use DynamicSearchBundle\DependencyInjection\Compiler\DefinitionBuilderPass;
use DynamicSearchBundle\DependencyInjection\Compiler\IndexPass;
use DynamicSearchBundle\DependencyInjection\Compiler\IndexProviderPass;
use DynamicSearchBundle\DependencyInjection\Compiler\OutputChannelPass;
use DynamicSearchBundle\DependencyInjection\Compiler\ResourceNormalizerPass;
use DynamicSearchBundle\DependencyInjection\Compiler\ResourceTransformerPass;
use DynamicSearchBundle\Provider\Extension\ProviderConfig;
use DynamicSearchBundle\Tool\Install;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DynamicSearchBundle extends AbstractPimcoreBundle implements DependentBundleInterface
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
        $container->addCompilerPass(new DefinitionBuilderPass());
        $container->addCompilerPass(new ResourceNormalizerPass());
        $container->addCompilerPass(new ResourceTransformerPass());
        $container->addCompilerPass(new IndexPass());
        $container->addCompilerPass(new OutputChannelPass());
        $container->addCompilerPass(new ContextGuardPass());
    }

    /**
     * {@inheritdoc}
     */
    public static function registerDependentBundles(BundleCollection $collection)
    {
        $providerConfig = new ProviderConfig();
        if ($providerConfig->configFileExists()) {
            foreach ($providerConfig->getAvailableProviderBundles() as $providerBundle) {
                $collection->addBundle(new $providerBundle());
            }
        }
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
