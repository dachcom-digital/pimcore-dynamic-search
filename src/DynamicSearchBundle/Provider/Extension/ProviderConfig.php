<?php

namespace DynamicSearchBundle\Provider\Extension;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use Symfony\Component\Yaml\Yaml;

class ProviderConfig
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $file;

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

        $yml = Yaml::dump($config);
        file_put_contents($this->locateConfigFile(), $yml);
    }

    /**
     * @return string
     */
    public function locateConfigFile()
    {
        if (null === $this->file) {
            $this->file = ConfigurationInterface::BUNDLE_PATH . '/enabled_providers.yml';
        }

        return $this->file;
    }

    /**
     * @return bool
     */
    public function configFileExists()
    {
        if (null !== $file = $this->locateConfigFile()) {
            return file_exists($file);
        }

        return false;
    }

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

}
