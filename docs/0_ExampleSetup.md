# Example Setup

This setup allows you a quick start with the bundle. The setup is tested within the official pimcore demo and uses class names and properties provided there.

Are you looking for an example of a more complex Pimcore setup? Here's another [example](02_MultiSiteMultiLocaleSetup.md).

## Installation

Add the following dependencies to your `composer.json`

```json
"require": {
    "dachcom-digital/dynamic-search": "~2.0.0",
    "dachcom-digital/dynamic-search-data-provider-trinity": "~2.0.0",
    "dachcom-digital/dynamic-search-index-provider-lucene": "~2.0.0",
}
```

And finalize the installation via command line

``` bash
composer upgrade
bin/console pimcore:bundle:enable DynamicSearchBundle
bin/console pimcore:bundle:install DynamicSearchBundle
```

You will also need to enable the bundles in your `config/bundles.php`

``` php
<?php

return [
    \DsTrinityDataBundle\DsTrinityDataBundle::class => ['all' => true],
    \DsLuceneBundle\DsLuceneBundle::class => ['all' => true],
];

```

## Symfony Configuration

Create a YAML file in your configuration directory

``` yaml
# config/config/dynamic-search.yaml
services:

    App\DynamicSearch\IndexDefinition\Trinity\Definition:
        tags:
            - { name: dynamic_search.document_definition_builder }

dynamic_search:

    context:

        # set a context with name "default"
        default:

            # set data provider
            data_provider:
                service: trinity_data
                options:
                    always:
                        index_object: true
                        object_class_names:
                            - Car
                        index_document: false
                        index_asset: false
                    full_dispatch:
                        document_limit: 10
                normalizer:
                    service: 'trinity_localized_resource_normalizer'

            # set index provider
            index_provider:
                service: 'lucene'
                options:
                    database_name: 'example_index_database'

            # build output channels
            output_channels:

                autocomplete:
                    service: 'lucene_autocomplete'

                suggestions:
                    service: 'lucene_suggestions'
                    #options:
                    #    restrict_search_fields:
                    #        - 'series'
                    normalizer:
                        service: 'lucene_document_key_value_normalizer'
                        #options:
                        #    skip_fields: ['name']

                search:
                    service: 'lucene_search'
                    internal: false
                    paginator:
                        enabled: true
                        # adapter_class: ''
                        max_per_page: 1
                    normalizer:
                        service: 'lucene_document_key_value_normalizer'
                        #options:
                        #    skip_fields: ['title']

                multi_search:
                    multiple: true
                    service: 'lucene_multi_search'
                    use_frontend_controller: true
                    blocks:
                        type1:
                            reference: search
                        type2:
                            reference: search
```

And link it into your main config. To do so update the imports block at the top. Alternatively, you can just add another `imports` at the bottom.

```yaml
# config/config/config.yaml
imports:
    - { resource: dynamic-search.yaml }

```

## Definition
```php
<?php

namespace App\DynamicSearch\IndexDefinition\Trinity;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

class Definition implements DocumentDefinitionBuilderInterface
{
    public function isApplicable(string $contextName, ResourceMetaInterface $resourceMeta): bool
    {
        if ($resourceMeta->getResourceCollectionType() !== 'object') {
            return false;
        }

        return true;
    }

    public function buildDefinition(DocumentDefinitionInterface $definition, array $normalizerOptions): DocumentDefinitionInterface
    {
        $definition
            ->addSimpleDocumentFieldDefinition([
                'name'              => 'pimcoreId',
                'index_transformer' => [
                    'type' => 'keyword',
                ],
                'data_transformer'  => [
                    'type'          => 'element_id_extractor'
                ]
            ])
            ->addSimpleDocumentFieldDefinition([
                'name'              => 'name',
                'index_transformer' => [
                    'type' => 'text',
                ],
                'data_transformer'  => [
                    'type'          => 'object_getter_extractor',
                    'configuration' => ['method' => 'getName']
                ]
            ])
            ->addPreProcessFieldDefinition([
                'type'          => 'object_relations_getter_extractor',
                'configuration' => [
                    'relations' => 'categories',
                    'method'    => 'getId',
                ]
            ], function (DocumentDefinitionInterface $definition, array $preProcessedTransformedData) {
                foreach ($preProcessedTransformedData as $categoryId) {
                    $definition->addSimpleDocumentFieldDefinition([
                        'name'              => sprintf('category_%d', $categoryId),
                        'index_transformer' => [
                            'type' => 'keyword',
                        ],
                        'data_transformer'  => [
                            'type'          => 'normalizer_value_callback',
                            'configuration' => ['value' => '1']
                        ]
                    ]);
                }
            });

        return $definition;
    }
}
```

## Indexing

To index your data, just execute:

``` bash
bin/console dynamic-search:run
```

## Test
You can test your setup in browser using the URL `/dynamic-search/default/j-search?q=xk140` where the `q` parameter is the search queue.
