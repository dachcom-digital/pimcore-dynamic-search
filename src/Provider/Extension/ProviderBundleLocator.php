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

namespace DynamicSearchBundle\Provider\Extension;

use Pimcore\Composer;
use Pimcore\Tool\ClassUtils;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ProviderBundleLocator implements ProviderBundleLocatorInterface
{
    public function __construct(
        protected Composer\PackageInfo $composerPackageInfo,
        protected array $availableBundles
    ) {
    }

    public function findProviderBundles(): array
    {
        $data = [];
        foreach ($this->findComposerBundles() as $bundleClass) {
            $data[] = [
                'path'   => $bundleClass,
                'active' => in_array($bundleClass, $this->availableBundles, true)
            ];
        }

        return $data;
    }

    /**
     * Finds composer bundles in /vendor
     * if composer package type is "dynamic-search-provider-bundle".
     */
    protected function findComposerBundles(): array
    {
        $pimcoreBundles = $this->composerPackageInfo->getInstalledPackages('dynamic-search-provider-bundle');

        $composerPaths = [];
        foreach ($pimcoreBundles as $packageInfo) {
            $composerPaths[] = PIMCORE_COMPOSER_PATH . '/' . $packageInfo['name'];
        }

        return $this->findBundlesInPaths($composerPaths);
    }

    protected function findBundlesInPaths(array $paths): array
    {
        if (empty($paths)) {
            return [];
        }

        $filteredPaths = [];
        foreach ($paths as $path) {
            if (file_exists($path) && is_dir($path)) {
                $filteredPaths[] = $path;
            }
        }

        $result = [];

        $finder = new Finder();
        $finder
            ->in(array_unique($filteredPaths))
            ->name('*Bundle.php');

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $className = ClassUtils::findClassName($file);
            if ($className) {
                $this->processBundleClass($className, $result);
            }
        }

        return $result;
    }

    protected function processBundleClass(?string $bundle, array &$result): void
    {
        if (empty($bundle)) {
            return;
        }

        if (!class_exists($bundle)) {
            return;
        }

        $reflector = new \ReflectionClass($bundle);

        if (!$reflector->isInstantiable() || !$reflector->implementsInterface(ProviderBundleInterface::class)) {
            return;
        }

        $result[$reflector->getName()] = $reflector->getName();
    }
}
