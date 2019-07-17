<?php

namespace DynamicSearchBundle\Provider\Extension;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ProviderConfig
{
    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $file;

    public function __construct()
    {
        $this->fileSystem = new FileSystem();
    }

    /**
     * @return array
     */
    public function getAvailableProviderBundles()
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

    /**
     * @param array $config
     */
    public function saveConfig(array $config)
    {
        $this->config = $config;

        if (!$this->fileSystem->exists($this->locateConfigDir())) {
            $this->fileSystem->mkdir($this->locateConfigDir(), 0755);
        }

        $this->fileSystem->dumpFile($this->locateConfigFile(), Yaml::dump($config));
    }

    /**
     * @return array
     */
    protected function loadConfig()
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

    /**
     * @return bool
     */
    public function configFileExists()
    {
        if (null !== $file = $this->locateConfigFile()) {
            return $this->fileSystem->exists($file);
        }

        return false;
    }

    /**
     * @return string
     */
    public function locateConfigFile()
    {
        if ($this->file === null) {
            $this->file = sprintf('%s/%s', $this->locateConfigDir(), 'enabled_providers.yml');
        }

        return $this->file;
    }

    /**
     * @return string
     */
    public function locateConfigDir()
    {
        return ConfigurationInterface::BUNDLE_PATH;
    }
}
