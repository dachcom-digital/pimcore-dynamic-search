<?php

namespace DynamicSearchBundle\Context;

class ContextDefinition implements ContextDefinitionInterface
{
    private string $dispatchType;
    private string $contextName;
    private array $contextOptions;
    private array $runtimeValues;

    public function __construct(string $dispatchType, string $contextName, array $options, array $runtimeValues = [])
    {
        $this->dispatchType = $dispatchType;
        $this->contextName = $contextName;
        $this->contextOptions = $options;
        $this->runtimeValues = $runtimeValues;
    }

    public function getName(): string
    {
        return $this->contextName;
    }

    public function getContextDispatchType(): string
    {
        return $this->dispatchType;
    }

    public function updateRuntimeValue(string $key, $value): void
    {
        $this->runtimeValues[$key] = $value;
    }

    public function getRuntimeValues(): array
    {
        return $this->runtimeValues;
    }

    public function getDataProviderOptions(string $providerBehaviour): array
    {
        $alwaysOptions = $this->contextOptions['data_provider']['options']['always'] ?? [];
        $behaviourOptions = $this->contextOptions['data_provider']['options'][$providerBehaviour] ?? [];

        return array_merge($alwaysOptions, $behaviourOptions);
    }

    public function getIndexProviderOptions(): array
    {
        return $this->contextOptions['index_provider']['options'] ?? [];
    }

    public function getResourceNormalizerOptions(): array
    {
        return $this->contextOptions['data_provider']['normalizer']['options'] ?? [];
    }

    public function getOutputChannelDocumentNormalizerOptions(string $outputChannelName): array
    {
        return $this->contextOptions['output_channels'][$outputChannelName]['normalizer']['options'] ?? [];
    }

    public function getOutputChannelOptions(string $outputChannelName): array
    {
        if (!isset($this->contextOptions['output_channels'][$outputChannelName])) {
            return [];
        }

        return $this->contextOptions['output_channels'][$outputChannelName]['options'] ?? [];
    }

    public function getOutputChannelPaginatorOptions(string $outputChannelName): array
    {
        return $this->contextOptions['output_channels'][$outputChannelName]['paginator'];
    }

    public function getDataProviderName(): string
    {
        return $this->contextOptions['data_provider']['service'];
    }

    public function getIndexProviderName(): string
    {
        return $this->contextOptions['index_provider']['service'];
    }

    public function getResourceNormalizerName(): string
    {
        return $this->contextOptions['data_provider']['normalizer']['service'];
    }

    public function getOutputChannelNormalizerName(string $outputChannelName): ?string
    {
        return $this->contextOptions['output_channels'][$outputChannelName]['normalizer']['service'] ?? null;
    }

    public function getOutputChannelServiceName(string $outputChannelName): ?string
    {
        if (!isset($this->contextOptions['output_channels'][$outputChannelName])) {
            return null;
        }

        return $this->contextOptions['output_channels'][$outputChannelName]['service'];
    }

    public function getOutputChannelEnvironment(string $outputChannelName): array
    {
        if (!isset($this->contextOptions['output_channels'][$outputChannelName])) {
            return [];
        }

        $config = $this->contextOptions['output_channels'][$outputChannelName];

        return [
            'internal'                => $config['internal'],
            'multiple'                => $config['multiple'],
            'use_frontend_controller' => $config['use_frontend_controller'],
            'blocks'                  => $config['blocks'] ?? [],
        ];
    }

    public function getOutputChannelRuntimeQueryProvider(string $outputChannelName): ?string
    {
        if (!isset($this->contextOptions['output_channels'][$outputChannelName])) {
            return null;
        }

        return $this->contextOptions['output_channels'][$outputChannelName]['runtime_query_provider'];
    }

    public function getOutputChannelRuntimeOptionsBuilder(string $outputChannelName): ?string
    {
        if (!isset($this->contextOptions['output_channels'][$outputChannelName])) {
            return null;
        }

        return $this->contextOptions['output_channels'][$outputChannelName]['runtime_options_builder'];
    }
}
