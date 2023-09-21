<?php

namespace DynamicSearchBundle\Tool;

use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Pimcore\Model\Translation;
use Pimcore\Tool\Admin;

class Install extends SettingsStoreAwareInstaller
{
    public function install(): void
    {
        $this->installTranslations();

        parent::install();
    }

    public function needsReloadAfterInstall(): bool
    {
        return false;
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
        return __DIR__ . '/../../config/install';
    }
}
