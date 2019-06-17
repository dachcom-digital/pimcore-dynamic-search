<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Provider\OutputChannel\AutoCompleteOutputChannelInterface;
use DynamicSearchBundle\Provider\OutputChannel\SearchOutputChannelInterface;

class IndexProviderRegistry implements IndexProviderRegistryInterface
{
    /**
     * @var array
     */
    protected $provider;

    /**
     * @var array
     */
    protected $outputChannel;

    /**
     * @param        $service
     * @param string $alias
     */
    public function register($service, string $alias)
    {
        if (!in_array(IndexProviderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), IndexProviderInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->provider[$alias] = $service;
    }

    /**
     * @param        $service
     * @param string $type
     * @param string $alias
     */
    public function registerOutputChannel($service, string $type, string $alias)
    {
        if ($type === 'autocomplete' && !in_array(AutoCompleteOutputChannelInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), AutoCompleteOutputChannelInterface::class,
                    implode(', ', class_implements($service)))
            );
        } elseif ($type === 'search' && !in_array(SearchOutputChannelInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), SearchOutputChannelInterface::class,
                    implode(', ', class_implements($service)))
            );
        }

        if (!isset($this->outputChannel[$type])) {
            $this->outputChannel[$type] = [];
        }

        $this->outputChannel[$type][$alias] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $alias)
    {
        return isset($this->provider[$alias]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $alias)
    {
        if (!$this->has($alias)) {
            throw new \Exception('"' . $alias . '" Index Provider does not exist');
        }

        return $this->provider[$alias];
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannel(string $type, string $alias)
    {
        return $this->outputChannel[$type][$alias];
    }
}