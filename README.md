# Pimcore - Dynamic Search

![Dynamic Search Schema](https://user-images.githubusercontent.com/700119/61217991-3c550c00-a711-11e9-9f62-6f1fb4ff0e3e.png)

[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/dynamic-search.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/dynamic-search)
[![Tests](https://img.shields.io/github/workflow/status/dachcom-digital/pimcore-dynamic-search/Codeception/master?style=flat-square&logo=github&label=codeception)](https://github.com/dachcom-digital/pimcore-dynamic-search/actions?query=workflow%3ACodeception+branch%3Amaster)
[![PhpStan](https://img.shields.io/github/workflow/status/dachcom-digital/pimcore-dynamic-search/PHP%20Stan/master?style=flat-square&logo=github&label=phpstan%20level%204)](https://github.com/dachcom-digital/pimcore-dynamic-search/actions?query=workflow%3A"PHP+Stan"+branch%3Amaster)

### Release Plan
| Release | Supported Pimcore Versions        | Supported Symfony Versions | Release Date | Maintained                       | Branch     |
|---------|-----------------------------------|----------------------------|--------------|----------------------------------|------------|
| **2.x** | `10.0`                            | `^5.4`                     | 19.12.2021   | Yes (Bugs, Features)             | master     |
| **1.x** | `6.6` - `6.9`                     | `^4.4`                     | 18.04.2021   | No | [1.x](https://github.com/dachcom-digital/pimcore-dynamic-search/tree/1.x) |

## Introduction
The Dynamic Search Bundle allows you to redefine your search strategy. 
It's based on several data- and index providers.

## Providers
There are several data- and index providers available:

### Data Provider
- [WebCrawler](https://github.com/dachcom-digital/pimcore-dynamic-search-data-provider-crawler) | Fetch data by crawling urls 
- [Trinity Data](https://github.com/dachcom-digital/pimcore-dynamic-search-data-provider-trinity) | Fetch pimcore entities: object, asset, document

### Index Provider
- [Lucene Search](https://github.com/dachcom-digital/pimcore-dynamic-search-index-provider-lucene) | Use the php lucene index. Not super-fast but comes without any dependencies but php
- [Elastic Search](https://github.com/dachcom-digital/pimcore-dynamic-search-index-provider-elasticsearch) | Index data with an elasticsearch instance.
- _apisearch.io_ | _coming soon_

## Installation  

```json
"require" : {
    "dachcom-digital/dynamic-search" : "~2.0.0"
}
```
### Installation via Extension Manager
After you have installed the Dynamic Search Bundle via composer, open pimcore backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Enable / Disable` row
- Click the green `+` Button in `Install/Uninstall` row

### Installation via CLI
- Execute: `$ bin/console pimcore:bundle:enable DynamicSearchBundle`
- Execute: `$ bin/console pimcore:bundle:install DynamicSearchBundle`

## Upgrading
- Execute: `$ bin/console doctrine:migrations:migrate --prefix 'DynamicSearchBundle\Migrations'`

## Provider Installation
You need at least one data- and one index provider. They have to be installed separately.
Please check out install instruction of each provider (see list above).

## Add Routes
```yaml
# app/config/routing.yml
dynamic_search_frontend:
    resource: '@DynamicSearchBundle/Resources/config/pimcore/routing/frontend_routing.yml'
```

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
- API

## Copyright and License
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.com)
For licensing details please visit [LICENSE.md](LICENSE.md)

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)
