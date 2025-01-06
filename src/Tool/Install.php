<?php

namespace DynamicSearchBundle\Tool;

use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;

class Install extends SettingsStoreAwareInstaller
{
    public function install(): void
    {
        parent::install();
    }

    public function needsReloadAfterInstall(): bool
    {
        return false;
    }

    protected function getInstallSourcesPath(): string
    {
        return __DIR__ . '/../../config/install';
    }
}
