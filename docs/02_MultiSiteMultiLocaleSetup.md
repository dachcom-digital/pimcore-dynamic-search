# Example Multi-Site / Multi-Locale Setup

This setup is an example how you can filter content in a multi-site/-locale Pimcore instance.
It is using the Trinity data provider and the Elasticsearch index provider to fetch and store data.

## Installation

Add the following dependencies to your `composer.json`

```json
"require": {
  "dachcom-digital/dynamic-search": "^2.0",
  "dachcom-digital/dynamic-search-data-provider-trinity": "^2.0",
  "dachcom-digital/dynamic-search-index-provider-elasticsearch": "^2.0"
}
```

And finalize the installation via command line

```bash
composer update
bin/console pimcore:bundle:enable DynamicSearchBundle
bin/console pimcore:bundle:install DynamicSearchBundle
```

You will also need to enable the bundles in your `config/bundles.php`

``` php
<?php

return [
    \DsTrinityDataBundle\DsTrinityDataBundle::class => ['all' => true],
    \DsElasticSearchBundle\DsElasticSearchBundle::class => ['all' => true],
];

```

## Symfony Configuration

Create a YAML file in your configuration directory.

``` yaml
# config/packages/dynamic-search.yaml
dynamic_search:
    # enable the pimcore element listener
    enable_pimcore_element_listener: true

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
                        index_asset: true
                normalizer:
                    service: 'trinity_localized_resource_normalizer'

            # set index provider
            index_provider:
                service: 'elasticsearch'
                options:
                    index:
                        identifier: 'my_project_prefix'
                        hosts:
                            - 'https://localhost:9200'
                        settings: []
                        credentials: []
                    analysis:
                        analyzer:
                            keyword_analyzer:
                                tokenizer: keyword
                                type: custom
                                filter:
                                    - lowercase
                                    - asciifolding
                                    - trim
                                char_filter: []
                            edge_ngram_analyzer:
                                tokenizer: edge_ngram_tokenizer
                                filter:
                                    - lowercase
                            edge_ngram_search_analyzer:
                                tokenizer: lowercase
                        tokenizer:
                            edge_ngram_tokenizer:
                                type: edge_ngram
                                min_gram: 2
                                max_gram: 5
                                token_chars:
                                    - letter

            # build output channels
            output_channels:

                search:
                    service: 'elasticsearch_search'
                    use_frontend_controller: true
                    options:
                        result_limit: 10
                    normalizer:
                        service: 'es_document_source_normalizer'
                    paginator:
                        enabled: true
                        max_per_page: 10
```

Make sure to import the config into your main config. To do so update the imports block at the top.

```yaml
# config/config.yaml
imports:
    - { resource: 'packages/*.yaml' }
```

With that the default configuration is finished. Now let's get to the interesting part: Make it work in a
multi-site/-locale environment.

## Definitions

First, we need the data definition files for all resources (Pimcore document, assets and objects) we want to index.
Here we have to make sure of one thing. All definitions have to return the exact same field definitions. In our example
the following fields are used:
- `type` (type of Pimcore element, one of "document", "asset", "object")
- `data_type` ("page" for documents, "document" for assets, "object_class_name" for objects)
- `title` (main title of the resource)
- `bodytext` (main content/text of the resource)
- `uri` (link to where the resource is available to look at)
- `locale` (language in which the resource is available)
- `siteId` (ID of the site in which the resource is available)

<details>
    <summary>Document Definition</summary>

```yaml
AppBundle\DynamicSearch\Definition\Trinity\DocumentDefinition:
    tags:
        - { name: dynamic_search.document_definition_builder }
```

```php
<?php

namespace App\DynamicSearch\Definition\Trinity;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionContextBuilderInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Provider\PreConfiguredIndexProviderInterface;

final class DocumentDefinition implements
    DocumentDefinitionBuilderInterface,
    DocumentDefinitionContextBuilderInterface,
    PreConfiguredIndexProviderInterface
{
    public function isApplicable(string $contextName, ResourceMetaInterface $resourceMeta): bool
    {
        return $resourceMeta->getResourceCollectionType() === 'document'
            && $resourceMeta->getResourceType() === 'page';
    }

    public function isApplicableForContext(string $contextName): bool
    {
        return true;
    }

    public function preConfigureIndex(IndexDocument $indexDocument): void
    {
    }

    public function buildDefinition(DocumentDefinitionInterface $definition, array $normalizerOptions): DocumentDefinitionInterface
    {
        return $definition
            ->addSimpleDocumentFieldDefinition([
                'name' => 'type',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'normalizer_value_callback',
                    'configuration' => [
                        'value' => 'document',
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'data_type',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'normalizer_value_callback',
                    'configuration' => [
                        'value' => 'page',
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'title',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'document_meta_extractor',
                    'configuration' => [
                        'type' => 'title',
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'bodytext',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'document_meta_extractor',
                    'configuration' => [
                        'type' => 'description',
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'uri',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'document_path_generator',
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'locale',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'element_property_extractor',
                    'configuration' => [
                        'property' => 'language',
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'siteId',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'document_site_extractor',
                ],
            ]);
    }
}
```
</details>

<details>
    <summary>Asset Definition</summary>

```yaml
AppBundle\DynamicSearch\Definition\Trinity\AssetDefinition:
    tags:
        - { name: dynamic_search.document_definition_builder }
```

```php
<?php

namespace App\DynamicSearch\Definition\Trinity;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionContextBuilderInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Provider\PreConfiguredIndexProviderInterface;

final class AssetDefinition implements
    DocumentDefinitionBuilderInterface,
    DocumentDefinitionContextBuilderInterface,
    PreConfiguredIndexProviderInterface
{
    public function isApplicable(string $contextName, ResourceMetaInterface $resourceMeta): bool
    {
        return $resourceMeta->getResourceCollectionType() === 'asset'
            && $resourceMeta->getResourceType() === 'document';
    }

    public function isApplicableForContext(string $contextName): bool
    {
        return true;
    }

    public function preConfigureIndex(IndexDocument $indexDocument): void
    {
    }

    public function buildDefinition(DocumentDefinitionInterface $definition, array $normalizerOptions): DocumentDefinitionInterface
    {
        return $definition
            ->addSimpleDocumentFieldDefinition([
                'name' => 'type',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'normalizer_value_callback',
                    'configuration' => [
                        'value' => 'asset',
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'data_type',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'normalizer_value_callback',
                    'configuration' => [
                        'value' => 'document',
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'title',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'asset_meta_extractor',
                    'configuration' => [
                        'name' => 'title',
                        'locale' => $normalizerOptions['locale'],
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'bodytext',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'asset_pdf_extractor',
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'uri',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'asset_path_generator',
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'locale',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'element_property_extractor',
                    'configuration' => [
                        'property' => 'language',
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'siteId',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'element_property_extractor',
                    'configuration' => [
                        'property' => 'site_id',
                    ],
                ],
            ]);
    }
}
```
</details>

<details>
    <summary>Object Definition</summary>

```yaml
AppBundle\DynamicSearch\Definition\Trinity\CarDefinition:
    tags:
        - { name: dynamic_search.document_definition_builder }
```

```php
<?php

namespace App\DynamicSearch\Definition\Trinity;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionContextBuilderInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Provider\PreConfiguredIndexProviderInterface;

final class CarDefinition implements
    DocumentDefinitionBuilderInterface,
    DocumentDefinitionContextBuilderInterface,
    PreConfiguredIndexProviderInterface
{
    public function isApplicable(string $contextName, ResourceMetaInterface $resourceMeta): bool
    {
        return $resourceMeta->getResourceCollectionType() === 'object'
            && $resourceMeta->getResourceSubType() === 'Car';
    }

    public function isApplicableForContext(string $contextName): bool
    {
        return true;
    }

    public function preConfigureIndex(IndexDocument $indexDocument): void
    {
    }

    public function buildDefinition(DocumentDefinitionInterface $definition, array $normalizerOptions): DocumentDefinitionInterface
    {
        return $definition
            ->addSimpleDocumentFieldDefinition([
                'name' => 'type',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'normalizer_value_callback',
                    'configuration' => [
                        'value' => 'object',
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'data_type',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'normalizer_value_callback',
                    'configuration' => [
                        'value' => 'car',
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'title',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'object_getter_extractor',
                    'configuration' => [
                        'method' => 'getName',
                        'arguments' => [$normalizerOptions['locale']],
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'bodytext',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'object_getter_extractor',
                    'configuration' => [
                        'method' => 'getDescription',
                        'arguments' => [$normalizerOptions['locale']],
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'uri',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'object_path_generator',
                    'configuration' => [
                        'arguments' => [
                            '_locale' => $normalizerOptions['locale'],
                        ],
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'locale',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'normalizer_value_callback',
                    'configuration' => [
                        'value' => $normalizerOptions['locale'],
                    ],
                ],
            ])
            ->addSimpleDocumentFieldDefinition([
                'name' => 'siteId',
                'index_transformer' => [
                    'type' => 'dynamic',
                ],
                'data_transformer' => [
                    'type' => 'object_site_extractor',
                ],
            ]);
    }
}
```
</details>

As you might have noticed, we are using two custom data transformers: `document_site_extractor` and `object_site_extractor`.
Let's see how they could look like ...

## Data Transformers

Extracting site ID's from a Pimcore document is really easy. We can use the `Pimcore\Tool\Frontend::getSiteForDocument($document)`
helper method to do so.

<details>
    <summary>Document Site Extractor</summary>

```yaml
AppBundle\DynamicSearch\Transformer\Field\DocumentSiteExtractor:
    tags:
        - {
            name: dynamic_search.resource.field_transformer,
            identifier: document_site_extractor,
            resource_scaffolder: trinity_data_scaffolder
        }
```

```php
<?php

namespace AppBundle\DynamicSearch\Transformer\Field;

use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Site;
use Pimcore\Tool;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DocumentSiteExtractor implements FieldTransformerInterface
{
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function setOptions(array $options)
    {
    }

    public function transformData(string $dispatchTransformerName, ResourceContainerInterface $resourceContainer)
    {
        $document = $resourceContainer->getResource();

        if (! $document instanceof Page) {
            return null;
        }

        $site = Tool\Frontend::getSiteForDocument($document);

        if (! $site instanceof Site) {
            return 0;
        }

        return $site->getId();
    }
}
```
</details>

In order to extract site ID's from an object, we have to implement a custom strategy. In this case we are using
a multiselect field, which has an option provider to make the site IDs available to the editor in the object.

<details>
    <summary>Object Site Extractor</summary>

```yaml
AppBundle\DynamicSearch\Transformer\Field\ObjectSiteExtractor:
    tags:
        - {
            name: dynamic_search.resource.field_transformer,
            identifier: object_site_extractor,
            resource_scaffolder: trinity_data_scaffolder
        }
```

```php
<?php

namespace AppBundle\DynamicSearch\Transformer\Field;

use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ObjectSiteExtractor implements FieldTransformerInterface
{
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function setOptions(array $options)
    {
    }

    public function transformData(string $dispatchTransformerName, ResourceContainerInterface $resourceContainer)
    {
        $object = $resourceContainer->getResource();

        if (! $object instanceof Concrete || !\method_exists($object, 'getSites')) {
            return null;
        }

        $sites = $object->getSites();

        // If no sites are selected, object is available for all sites.
        if (empty($sites)) {
            $options = Service::getOptionsForMultiSelectField($object, 'sites');

            return \array_keys($options);
        }

        return $sites;
    }
}
```
</details>

Awesome! Now we are ready to index our data.

## Indexing data
To index your data, execute the following in the command line.

``` bash
bin/console dynamic-search:run
```

## Querying the data
Now there's one thing left to do; outputting the data! Remember, we indexed all of our data for all locales and sites.
Now we need to make sure to only display the data that fits the context (current locale and site).

To do so, we need to manipulate the default search mechanism. Let create our output channel modifier class.

<details>
    <summary>Output Channel Modifier</summary>

```yaml
AppBundle\DynamicSearch\OutputChannel\Modifier\SiteLocaleRestrictionAction:
    tags:
        - {
            name: dynamic_search.output_channel.modifier.action,
            output_channel_service_identifier: all,
            action: post_query_build
        }
```

```php
<?php

namespace AppBundle\DynamicSearch\OutputChannel\Modifier;

use DynamicSearchBundle\Event\OutputModifierEvent;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchPhraseQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\QueryStringQuery;
use ONGR\ElasticsearchDSL\Query\FullText\SimpleQueryStringQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\PrefixQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\RequestStack;

class SiteLocaleRestrictionAction implements OutputChannelModifierActionInterface
{
    protected RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function dispatchAction(string $action, OutputChannelAllocatorInterface $outputChannelAllocator, OutputModifierEvent $event): OutputModifierEvent
    {
        /** @var Search $search */
        $search = $event->getParameter('query');

        // Add SimpleQueryString query
        $simpleQueryStringQuery = new SimpleQueryStringQuery($event->getParameter('term'), [
            'fields' => ['title^100', 'bodytext^50'],
        ]);
        $search->addQuery($simpleQueryStringQuery);

        $boolQuery = new BoolQuery();

        // Add locale filter
        $locale = $this->requestStack->getMainRequest()->query->get('locale');

        if ($locale === null) {
            $locale = $this->requestStack->getMainRequest()->getLocale();
        }

        if ($locale !== null) {
            $localeQuery = new MatchPhraseQuery('locale', $locale);
            $boolQuery->add($localeQuery, BoolQuery::FILTER);
        }

        // Add site filter
        $siteId = Site::isSiteRequest() ? Site::getCurrentSite()->getId() : 0;
        $siteQuery = new TermQuery('siteId', $siteId);
        $boolQuery->add($siteQuery, BoolQuery::FILTER);

        // Add bool query
        $search->addQuery($boolQuery);
        $event->setParameter('query', $search);

        return $event;
    }
}
```
</details>

And that's it! We are now able to search contents of a multi-site/-locale Pimcore instance.
