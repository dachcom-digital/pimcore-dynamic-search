# Output Channels

Output channels in general control how data is fetched, as well as the output of the retrieved data.
The concept of output channels offers high flexibility since there's no limitation regarding the search index provider and how data is queried and output.

## Configuration

### General
`dynamic_search.context.default.output_channels.<output-channel-name>`

| property                   | description                                                                                                                       | default                                                |
|----------------------------|-----------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------|
| `multiple`                 | wether this output channel is a composition of multiple search containers                                                         | `false`                                                |
| `service`                  | service for the output channel. must implement `OutputChannelInterface` or `MultiOutputChannelInterface`, if `multiple` is `true` | `null`                                                 |
| `use_frontend_controller`  | wether to use the `SearchFrontendController` for twig output, or the `SearchController` for json output                           | `false`                                                |
| `view_name`                | name of the view to use. only effective if `use_frontend_controller` is `true`                                                    | `MultiList` if `multiple` is `true`, `List` otherwise. |
| `runtime_query_provider`   | service for query provider. must implement `RuntimeQueryProviderInterface`                                                        | `default`                                              |
| `runtime_options_builder`  | service for options builder. must implement `RuntimeOptionsBuilderInterface`                                                      | `default`                                              |
| `internal`                 | for internal use                                                                                                                  | `false`                                                |

### Options
`dynamic_search.context.default.output_channels.<output-channel-name>.options`

options for the output channel go here

### Paginator
`dynamic_search.context.default.output_channels.<output-channel-name>.paginator`

| property        | description                                                         | default                                                      |
|-----------------|---------------------------------------------------------------------|--------------------------------------------------------------|
| `enabled`       | wether paging is enabled                                            | `false`                                                      |
| `adapter_class` | implementation to use for paging. must implement `AdapterInterface` | `DynamicSearchBundle\Paginator\Adapter\DynamicSearchAdapter` |
| `max_per_page`  | max results per page                                                | `10`                                                         |

### Normalizer

| property      | description                                                                                  | default |
|---------------|----------------------------------------------------------------------------------------------|---------|
| `service`     | service to use as normalizer for each document. Must implement `DocumentNormalizerInterface` | `null`  |

## Further reading

Check out the following implementations of `OutputChannelInterface`:
- [SearchOutputChannel](https://github.com/dachcom-digital/pimcore-dynamic-search-index-provider-elasticsearch/blob/master/src/DsElasticSearchBundle/OutputChannel/SearchOutputChannel.php) of `pimcore-dynamic-search-index-provider-elasticsearch`
- [SearchOutputChannel](https://github.com/dachcom-digital/pimcore-dynamic-search-index-provider-opensearch/blob/main/src/DsOpenSearchBundle/OutputChannel/SearchOutputChannel.php) of `pimcore-dynamic-search-index-provider-opensearch`
- [SearchOutputChannel](https://github.com/dachcom-digital/pimcore-dynamic-search-index-provider-lucene/blob/master/src/DsLuceneBundle/OutputChannel/SearchOutputChannel.php) of `pimcore-dynamic-search-index-provider-lucene`
