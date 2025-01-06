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
# config/packages/dynamic-search.yaml
services:

    App\DynamicSearch\IndexDefinition\TrinityDocument:
        tags:
            - { name: dynamic_search.document_definition_builder }

    App\DynamicSearch\IndexDefinition\Object\Car:
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
                        index_document: true
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
                        max_per_page: 10
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

Symfony will detect the config in `config/packages` automatically, you do not have to link it.

## Definition for a DataObject
```php
<?php

namespace App\DynamicSearch\IndexDefinition\Object;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

class Car implements DocumentDefinitionBuilderInterface
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

## Definition for a Document

This definition will only save index for the id, title and meta description. If you want to save the content of the page you will have to use the [WebCrawler](https://github.com/dachcom-digital/pimcore-dynamic-search-data-provider-crawler) or read the content using callbacks. This might be a bit more complex, as you need to consider the concept of personalized content and your own set of bricks.

``` php
<?php

namespace App\DynamicSearch\IndexDefinition;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

class TrinityDocument implements DocumentDefinitionBuilderInterface
{
    public function isApplicable(string $contextName, ResourceMetaInterface $resourceMeta): bool
    {
        if ($resourceMeta->getResourceCollectionType() !== 'document') {
            return false;
        }

        return true;
    }

    public function buildDefinition(DocumentDefinitionInterface $definition, array $normalizerOptions): DocumentDefinitionInterface
    {
        $definition
            ->addSimpleDocumentFieldDefinition([
                'name'              => 'id',
                'index_transformer' => [
                    'type' => 'keyword',
                ],
                'data_transformer'  => [
                    'type'          => 'element_id_extractor',
                ]
            ])
            ->addSimpleDocumentFieldDefinition([
                'name'              => 'path',
                'index_transformer' => [
                    'type' => 'text',
                ],
                'data_transformer'  => [
                    'type'          => 'document_path_generator',
                ]
            ])
            ->addSimpleDocumentFieldDefinition([
                'name'              => 'title',
                'index_transformer' => [
                    'type' => 'text',
                ],
                'data_transformer'  => [
                    'type'          => 'document_meta_extractor',
                    'configuration' => [
                        'type' => 'title',
                    ],
                ]
            ])
            ->addSimpleDocumentFieldDefinition([
                'name'              => 'description',
                'index_transformer' => [
                    'type' => 'text',
                ],
                'data_transformer'  => [
                    'type'          => 'document_meta_extractor',
                    'configuration' => [
                        'type' => 'description',
                    ],
                ]
            ]);

        return $definition;
    }
}
```

## Indexing

To index your data, execute following in the command line:

``` bash
bin/console dynamic-search:run -v
```

The parameter `-v` will make thoe output more verbose and can help you to find any problems in the config.

## Test
You can test your setup in browser using the URL `/dynamic-search/default/j-search?q=xk140` where the `q` parameter is the search queue.
