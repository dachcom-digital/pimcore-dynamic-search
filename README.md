# Pimcore - Dynamic Search

![Dynamic Search Schema](https://user-images.githubusercontent.com/700119/61217991-3c550c00-a711-11e9-9f62-6f1fb4ff0e3e.png)

[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/dynamic-search.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/dynamic-search)
[![Tests](https://img.shields.io/github/actions/workflow/status/dachcom-digital/pimcore-dynamic-search/.github/workflows/codeception.yml?branch=master&style=flat-square&logo=github&label=codeception)](https://github.com/dachcom-digital/pimcore-dynamic-search/actions?query=workflow%3ACodeception+branch%3Amaster)
[![PhpStan](https://img.shields.io/github/actions/workflow/status/dachcom-digital/pimcore-dynamic-search/.github/workflows/php-stan.yml?branch=master&style=flat-square&logo=github&label=phpstan%20level%204)](https://github.com/dachcom-digital/pimcore-dynamic-search/actions?query=workflow%3A"PHP+Stan"+branch%3Amaster)

### Release Plan
| Release | Supported Pimcore Versions | Supported Symfony Versions | Release Date | Maintained     | Branch                                                                    |
|---------|----------------------------|----------------------------|--------------|----------------|---------------------------------------------------------------------------|
| **4.x** | `11.0`                     | `^6.2`                     | --           | Feature Branch | master                                                                    |
| **3.x** | `11.0`                     | `^6.2`                     | 28.09.2023   | Bugfixes       | [3.x](https://github.com/dachcom-digital/pimcore-dynamic-search/tree/3.x) |
| **2.x** | `10.0` - `10.6`            | `^5.4`                     | 19.12.2021   | No             | [2.x](https://github.com/dachcom-digital/pimcore-dynamic-search/tree/2.x) |
| **1.x** | `6.6` - `6.9`              | `^4.4`                     | 18.04.2021   | No             | [1.x](https://github.com/dachcom-digital/pimcore-dynamic-search/tree/1.x) |

## Introduction
The Dynamic Search Bundle allows you to redefine your search strategy. 
It's based on several data- and index providers.

## Providers
There are several data- and index providers available:

### Data Provider
- [WebCrawler](https://github.com/dachcom-digital/pimcore-dynamic-search-data-provider-crawler) | Fetch data by crawling urls 
- [Trinity Data](https://github.com/dachcom-digital/pimcore-dynamic-search-data-provider-trinity) | Fetch pimcore entities: object, asset, document

### Index Provider
- [Lucene Search](https://github.com/dachcom-digital/pimcore-dynamic-search-index-provider-lucene) | Use the php lucene index. Not superfast but comes without any dependencies but php
- [Elasticsearch](https://github.com/dachcom-digital/pimcore-dynamic-search-index-provider-elasticsearch) | Index data with an elasticsearch instance.
- [Open Search](https://github.com/dachcom-digital/pimcore-dynamic-search-index-provider-opensearch) | Index data with an open search instance.

## Installation  

```json
"require" : {
    "dachcom-digital/dynamic-search" : "~4.0.0"
}
```

Add Bundle to `bundles.php`:
```php
return [
    DynamicSearchBundle\DynamicSearchBundle::class => ['all' => true],
];
```

- Execute: `$ bin/console pimcore:bundle:install DynamicSearchBundle`
- Execute optionally: `$ bin/console messenger:setup-transports`

## Upgrading
- Execute: `$ bin/console doctrine:migrations:migrate --prefix 'DynamicSearchBundle\Migrations'`

## Provider Installation
You need at least one data- and one index provider. They have to be installed separately.
Please check out install instruction of each provider (see list above).

## Add Routes
```yaml
# config/routes.yaml
dynamic_search_frontend:
    resource: '@DynamicSearchBundle/config/pimcore/routing/frontend_routing.yaml'
```

## Start Queue Worker
```
$ bin/console messenger:consume dynamic_search_queue
```

Read more details about the queue worker and the recommended setup [here](docs/01_DispatchWorkflow.md#queue-worker).


## Dispatch Dynamic Search
After you've added [a definition](docs/0_ExampleSetup.md), you're ready to start the engine.
Always use the verbose `-v` flag, otherwise you won't get any process information about the ongoing data / index providing process.

```bash
$ bin/console dynamic-search:run -v
```

## Further Information
![image](https://user-images.githubusercontent.com/700119/146414238-ad2964e6-e873-4607-a89b-bc2ec2e5b95c.png)

- [Example Setup](docs/0_ExampleSetup.md)
- [Dispatch Workflow](docs/01_DispatchWorkflow.md)
- Configuration
    - [Context Guard](docs/200_ContextGuard.md)
    - Document Definition
    - Logging
- Data Creation
    - [Resource Validation](docs/40_ResourceValidator.md)
    - Enable automatic Update / Insert / Delete Service
- Data Fetching
    - [Output Channels](docs/30_OutputChannels.md)
        - [Create Output Channel](docs/300_CreateOutputChannel.md)
        - [Channel Filter / Actions](docs/302_ChannelFilterActions.md)
        - Multi Search Channels
    - Filter (Faceted Search / Aggregation)
        - Create Filter Definition
- [Backend UI](docs/50_BackendUI.md)
- API


## Copyright and License
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.com)
For licensing details please visit [LICENSE.md](LICENSE.md)

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)
