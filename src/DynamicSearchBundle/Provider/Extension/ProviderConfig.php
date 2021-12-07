<?php

namespace DynamicSearchBundle\Provider\Extension;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ProviderConfig
{
    protected FileSystem $fileSystem;
    protected array $config = [];
    protected ?string $file = null;

    public function __construct()
    {
        $this->fileSystem = new FileSystem();
    }

    public function getAvailableProviderBundles(): array
    {
        $config = $this->loadConfig();

        if (!isset($config['dynamic_search_provider_bundles'])) {
            return [];
        }

        if (!is_array($config['dynamic_search_provider_bundles'])) {
            return [];
        }

        return $config['dynamic_search_provider_bundles'];
    }

    public function saveConfig(array $config): void
    {
        $this->config = $config;

        if (!$this->fileSystem->exists($this->locateConfigDir())) {
            $this->fileSystem->mkdir($this->locateConfigDir(), 0755);
        }

        $this->fileSystem->dumpFile($this->locateConfigFile(), Yaml::dump($config));
    }

    protected function loadConfig(): array
    {
        if (!$this->config) {
            if ($this->configFileExists()) {
                $this->config = Yaml::parse(file_get_contents($this->locateConfigFile()));
            }

            if (!$this->config) {
                $this->config = [];
            }
        }

        return $this->config;
    }

    public function configFileExists(): bool
    {
        $file = $this->locateConfigFile();

        return $this->fileSystem->exists($file);
    }

    public function locateConfigFile(): string
    {
        if ($this->file === null) {
            $this->file = sprintf('%s/%s', $this->locateConfigDir(), 'enabled_providers.yml');
        }

        return $this->file;
    }

    public function locateConfigDir(): string
    {
        return ConfigurationInterface::BUNDLE_PATH;
    }
}
