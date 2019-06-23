# Example Setup

### composer.json
```json
"require" : {
    "dachcom-digital/dynamic-search": "~0.1.0",
    "dachcom-digital/dynamic-search-data-provider-crawler": "~0.1.0",
    "dachcom-digital/dynamic-search-data-provider-trinity": "~0.1.0",
    "dachcom-digital/dynamic-search-index-provider-lucene": "~0.1.0",
}
```

### app/config/routing.yml
```yaml
dynamic_search_frontend:
    resource: '@DynamicSearchBundle/Resources/config/pimcore/routing/frontend_routing.yml'
```

### app/config/config.yml
```yaml
dynamic_search:

    context:

        ecommerce:

            index_provider:
                service: 'lucene'
                options:
                    database_name: 'ecommerce'

            data_provider:
                service: 'trinityData'
                options:
                    index_object: true
                    object_class_names:
                        - TestClass
                    index_document: true
                    index_asset: true

            output_channels:
                autocomplete:
                    service: 'lucene'
                suggestions:
                    service: 'lucene'
                    options:
                        restrict_search_fields:
                            - 'sku'
                search:
                    service: 'lucene'
                    options:
                        max_per_page: 4

            data_transformer:

                document:
                    id:
                        field_transformer: 'element_id_extractor'

                fields:

                    sku:
                        index_type: 'keyword'
                        field_transformer: 'object_getter_extractor'
                        field_transformer_options:
                            argument: 'getSku'

                    title:
                        index_type: 'text'
                        field_transformer: 'object_getter_extractor'
                        field_transformer_options:
                            argument: 'getTitle'

                    image:
                        index_type: 'text'
                        field_transformer: 'object_getter_extractor'
                        field_transformer_options:
                            argument: 'getImage'

        default:

            index_provider:
                service: 'lucene'
                options:
                    database_name: 'default'

            data_provider:
                service: 'webCrawler'
                options:
                    seed: 'http://dev-site.test/de'
                    valid_links:
                        - '@^http://dev-site.test.*@i'
                    user_invalid_links:
                        - '@^http://dev-site.test\/members.*@i'

            output_channels:
                autocomplete:
                    service: 'lucene'
                    runtime_options_provider: 'my-special-runtime-options-provider'
                suggestions:
                    service: 'lucene'
                    options:
                        restrict_search_fields:
                            - 'main_headline'
                search:
                    service: 'lucene'
                    options:
                        max_per_page: 4

            data_transformer:

                document:
                    id:
                        field_transformer: 'resource_document_id_extractor'
                    boost:
                        field_transformer: 'resource_meta_extractor'
                        field_transformer_options:
                            name: 'boost'

                fields:

                    uri:
                        index_type: 'keyword'
                        field_transformer: 'resource_uri_extractor'

                    host:
                        index_type: 'keyword'
                        field_transformer: 'resource_host_extractor'

                    language:
                        index_type: 'keyword'
                        field_transformer: 'resource_language_extractor'

                    title:
                        index_type: 'text'
                        field_transformer: 'resource_title_extractor'

                    main_headline:
                        index_type: 'text'
                        field_transformer: 'resource_html_tag_content_extractor'
                        field_transformer_options:
                            tag: 'h1'
                            return_multiple: false

                    meta_description:
                        index_type: 'text'
                        field_transformer: 'resource_meta_extractor'
                        field_transformer_options:
                            name: 'description'

                    content:
                        index_type: 'text'
                        field_transformer: 'resource_text_extractor'
                        field_transformer_options:
                            content_start_indicator: '<!-- main-content -->'
                            content_end_indicator: '<!-- /main-content -->'
                            # content_exclude_start_indicator: ''
                            # content_exclude_end_indicator: ''
                        output_channel:
                            visibility:
                                suggestions: false
```