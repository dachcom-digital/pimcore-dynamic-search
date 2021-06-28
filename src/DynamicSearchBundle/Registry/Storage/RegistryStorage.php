<?php

namespace DynamicSearchBundle\Registry\Storage;

class RegistryStorage
{
    protected array $store = [];

    public function store($service, string $requiredInterface, string $namespace, string $identifier, ?string $alias = null, bool $allowMultipleAppearance = false)
    {
        if (!isset($this->store[$namespace])) {
            $this->store[$namespace] = [];
        }

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

    public function has(string $namespace, string $identififer)
    {
        return $this->get($namespace, $identififer) !== null;
    }

    public function hasOneByNamespace(string $namespace)
    {
        return count(array_filter($this->store, static function ($entry) use ($namespace) {
                return $entry['namespace'] === $namespace;
            })) > 0;
    }

    public function get(string $namespace, string $identifier)
    {
        if (null !== $byIdentifier = $this->getByIdentifier($namespace, $identifier)) {
            return $byIdentifier;
        }

        if (null !== $byAlias = $this->getByAlias($namespace, $identifier)) {
            return $byAlias;
        }

        return null;
    }

    public function getByNamespace(string $namespace): array
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

    protected function getByIdentifier(string $namespace, string $identififer)
    {
        foreach ($this->store as $entry) {
            if ($entry['namespace'] === $namespace && $entry['identifier'] === $identififer) {
                return $entry['service'];
            }
        }

        return null;
    }

    protected function getByAlias(string $namespace, string $alias)
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
