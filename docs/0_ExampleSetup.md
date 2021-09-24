# Example Setup

### composer.json
```json
"require" : {
    "dachcom-digital/dynamic-search": "~1.0.0",
    "dachcom-digital/dynamic-search-data-provider-trinity": "~1.0.0",
    "dachcom-digital/dynamic-search-index-provider-lucene": "~1.0.0",
}
```

### app/config/routing.yml
```yaml
dynamic_search_frontend:
    resource: '@DynamicSearchBundle/Resources/config/pimcore/routing/frontend_routing.yml'
```

### app/config/config.yml
```yaml

services:

    AppBundle\DynamicSearch\IndexDefinition\Trinity\Definition:
        tags:
            - { name: dynamic_search.document_definition_builder }
            
dynamic_search:

    context:

        default:

            data_provider:
                service: 'trinity_data'
                options:
                    always:
                        index_object: true
                        object_class_names:
                            - MyTestClass
                        index_document: true
                        index_asset: false
                    full_dispatch:
                        object_limit: 20
                        document_limit: 10

                normalizer:
                    service: 'trinity_localized_resource_normalizer'

            index_provider:
                service: 'lucene'
                options:
                    database_name: 'my_index_database'

            output_channels:
                autocomplete:
                    service: 'lucene_autocomplete'
                suggestions:
                    service: 'lucene_suggestions'
                    #options:
                    #    restrict_search_fields:
                    #        - 'sku'
                    normalizer:
                        service: 'lucene_document_key_value_normalizer'
                        #options:
                        #    skip_fields: ['title']
                search:
                    service: 'lucene_search'
                    internal: false
                    use_frontend_controller: true
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

### AppBundle\DynamicSearch\IndexDefinition\Trinity\Definition.php
```php
<?php

namespace AppBundle\DynamicSearch\IndexDefinition\Trinity;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

class Definition implements DocumentDefinitionBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function isApplicable(string $contextName, ResourceMetaInterface $resourceMeta)
    {
        if ($resourceMeta->getResourceCollectionType() !== 'object') {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function buildDefinition(DocumentDefinitionInterface $definition, array $normalizerOptions)
    {
        $definition
            ->addSimpleDocumentFieldDefinition([
                'name'              => 'sku',
                'index_transformer' => [
                    'type' => 'text',
                ],
                'data_transformer'  => [
                    'type'          => 'object_getter_extractor',
                    'configuration' => ['method' => 'getSku']
                ]
            ])
            ->addSimpleDocumentFieldDefinition([
                'name'              => 'title',
                'index_transformer' => [
                    'type' => 'text',
                ],
                'data_transformer'  => [
                    'type'          => 'object_getter_extractor',
                    'configuration' => ['method' => 'getTitle']
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
                        'value'             => '1'
                    ]);
                }
            });
    }
}
```
