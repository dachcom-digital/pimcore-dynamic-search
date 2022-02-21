<?php

namespace DynamicSearchBundle\Registry\Storage;

use Symfony\Component\DependencyInjection\Reference;

class RegistryStorage
{
    /**
     * @var array
     */
    protected $store;

    public function __construct()
    {
        $this->store = [];
    }

    /**
     * @param Reference   $service
     * @param string      $requiredInterface
     * @param string      $namespace
     * @param string      $identifier
     * @param string|null $alias
     * @param bool        $allowMultipleAppearance
     *
     * @throws \Exception
     */
    public function store($service, $requiredInterface, $namespace, $identifier, $alias = null, $allowMultipleAppearance = false)
    {
        if (!in_array($requiredInterface, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s needs to implement "%s", "%s" given.',
                    get_class($service),
                    $requiredInterface,
                    implode(', ', class_implements($service))
                )
            );
        }

        if ($allowMultipleAppearance === false) {

            if ($identifier === $alias) {
                throw new \Exception(sprintf('Alias "%s" for Service "%s" cannot be identical', $alias, $identifier));
            }

            if ($this->getByIdentifier($namespace, $identifier) !== null) {
                throw new \Exception(sprintf('Service "%s" for namespace "%s" already has been registered', $identifier, $namespace));
            } elseif ($this->getByAlias($namespace, $alias) !== null) {
                throw new \Exception(sprintf('Service "%s" for namespace "%s" with alias "%s" already has been registered', $identifier, $namespace, $alias));
            }

        }

        $this->store[] = [
            'identifier' => $identifier,
            'namespace'  => $namespace,
            'alias'      => $alias,
            'service'    => $service,
            'multiple'   => $allowMultipleAppearance
        ];
    }

    /**
     * @param string $namespace
     * @param string $identififer
     *
     * @return bool
     */
    public function has($namespace, $identififer)
    {
        return $this->get($namespace, $identififer) !== null;
    }

    /**
     * @param string $namespace
     *
     * @return array
     */
    public function hasOneByNamespace($namespace)
    {
        return count(array_filter($this->store, function ($entry) use ($namespace) {
                return $entry['namespace'] === $namespace;
            })) > 0;
    }

    /**
     * @param string $namespace
     * @param string $identififer
     *
     * @return mixed|null
     */
    public function get($namespace, $identififer)
    {
        if (null !== $byIdentifier = $this->getByIdentifier($namespace, $identififer)) {
            return $byIdentifier;
        } elseif (null !== $byAlias = $this->getByAlias($namespace, $identififer)) {
            return $byAlias;
        }

        return null;
    }

    /**
     * @param string $namespace
     *
     * @return array
     */
    public function getByNamespace($namespace)
    {
        $validRows = array_filter($this->store, function ($entry) use ($namespace) {
            return $entry['namespace'] === $namespace;
        });

        $items = [];
        foreach ($validRows as $row) {
            $identifier = $row['alias'] !== null ? $row['alias'] : $row['identifier'];
            if ($identifier === null) {
                $items[] = $row['service'];
            } else {
                $items[$identifier] = $row['service'];
            }
        }

        return $items;
    }

    /**
     * @param string $namespace
     * @param string $identififer
     *
     * @return mixed|null
     */
    protected function getByIdentifier($namespace, $identififer)
    {
        foreach ($this->store as $entry) {
            if ($entry['namespace'] === $namespace && $entry['identifier'] === $identififer) {
                return $entry['service'];
            }
        }

        return null;
    }

    /**
     * @param string      $namespace
     * @param string|null $alias
     *
     * @return mixed|null
     */
    protected function getByAlias($namespace, $alias)
    {
        if ($alias === null) {
            return null;
        }

        foreach ($this->store as $entry) {
            if ($entry['namespace'] === $namespace && $entry['alias'] === $alias) {
                return $entry['service'];
            }
        }

        return null;
    }

}
