<?php

namespace DynamicSearchBundle\Tool;

use DynamicSearchBundle\Manager\ProviderBundleManagerInterface;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Pimcore\Model\Property;
use Pimcore\Model\Translation;
use Pimcore\Tool\Admin;

class Install extends SettingsStoreAwareInstaller
{
    protected ProviderBundleManagerInterface $providerBundleManager;

    public function setProviderBundleManager(ProviderBundleManagerInterface $providerBundleManager): void
    {
        $this->providerBundleManager = $providerBundleManager;
    }

    public function install(): void
    {
        $this->installTranslations();
        $this->installProperties();

        parent::install();
    }

    public function needsReloadAfterInstall(): bool
    {
        return false;
    }

    /**
     * @throws InstallationException
     */
    private function installProperties(): void
    {
        $properties = [];

        foreach ($properties as $key => $propertyConfig) {
            $defProperty = Property\Predefined::getByKey($key);

            if ($defProperty instanceof Property\Predefined) {
                continue;
            }

            $property = new Property\Predefined();
            $property->setKey($key);
            $property->setType($propertyConfig['type']);
            $property->setName($propertyConfig['name']);
            $property->setDescription($propertyConfig['description']);
            $property->setCtype($propertyConfig['ctype']);
            $property->setConfig($propertyConfig['config']);
            $property->setInheritable(false);

            try {
                $property->getDao()->save();
            } catch (\Exception $e) {
                throw new InstallationException(sprintf('Failed to save document property "%s". Error was: "%s"', $propertyConfig['name'], $e->getMessage()));
            }
        }
    }

    private function installTranslations(): void
    {
        $csvAdmin = $this->getInstallSourcesPath() . '/translations/admin.csv';
        $csvFrontend = $this->getInstallSourcesPath() . '/translations/frontend.csv';

        try {
            Translation::importTranslationsFromFile($csvAdmin, Translation::DOMAIN_ADMIN, true, Admin::getLanguages());
        } catch (\Exception $e) {
            throw new InstallationException(sprintf('Failed to install admin translations. error was: "%s"', $e->getMessage()));
        }

        try {
            Translation::importTranslationsFromFile($csvFrontend, Translation::DOMAIN_DEFAULT, true, Admin::getLanguages());
        } catch (\Exception $e) {
            throw new InstallationException(sprintf('Failed to install website translations. error was: "%s"', $e->getMessage()));
        }
    }

    protected function getInstallSourcesPath(): string
    {
        return __DIR__ . '/../Resources/install';
    }
}
