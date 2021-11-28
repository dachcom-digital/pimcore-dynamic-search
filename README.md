# Pimcore - Dynamic Search

![Dynamic Search Schema](https://user-images.githubusercontent.com/700119/61217991-3c550c00-a711-11e9-9f62-6f1fb4ff0e3e.png)

[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/dynamic-search.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/dynamic-search)
[![Tests](https://img.shields.io/github/workflow/status/dachcom-digital/pimcore-dynamic-search/Codeception/master?style=flat-square&logo=github&label=codeception)](https://github.com/dachcom-digital/pimcore-dynamic-search/actions?query=workflow%3ACodeception+branch%3Amaster)
[![PhpStan](https://img.shields.io/github/workflow/status/dachcom-digital/pimcore-dynamic-search/PHP%20Stan/master?style=flat-square&logo=github&label=phpstan%20level%204)](https://github.com/dachcom-digital/pimcore-dynamic-search/actions?query=workflow%3A"PHP+Stan"+branch%3Amaster)

### Release Plan

| Release | Supported Pimcore Versions        | Supported Symfony Versions | Release Date | Maintained                       | Branch     |
|---------|-----------------------------------|----------------------------|--------------|----------------------------------|------------|
| **2.x** | `10.0`                            | `^5.2`                     | no release   | Yes (Bugs, Features)             | master     |
| **1.x** | `6.6` - `6.9`                     | `^4.4`                     | 18.04.2021   | Yes (Bugs, Features if required) | [1.x](https://github.com/dachcom-digital/pimcore-dynamic-search/tree/1.x) |

## Introduction
The Dynamic Search Bundle allows you to redefine your search strategy. It's based on several data- and index providers.

### Data Provider
- [WebCrawler](https://github.com/dachcom-digital/pimcore-dynamic-search-data-provider-crawler) (Spider)
- [Trinity Data](https://github.com/dachcom-digital/pimcore-dynamic-search-data-provider-trinity) (Pimcore: Object, Asset, Document)

### Index Provider
- [Lucene Search](https://github.com/dachcom-digital/pimcore-dynamic-search-index-provider-lucene)
- [Elastic Search](https://github.com/dachcom-digital/pimcore-dynamic-search-index-provider-elasticsearch)
- _apisearch.io (coming soon)_

### Installation  

```json
"require" : {
    "dachcom-digital/dynamic-search" : "~2.0.0"
}
```

- Execute: `$ bin/console pimcore:bundle:enable DynamicSearchBundle`
- Execute: `$ bin/console pimcore:bundle:install DynamicSearchBundle`

## Upgrading
- Execute: `$ bin/console doctrine:migrations:migrate --prefix 'DynamicSearchBundle\Migrations'`

## Further Information
- [Example Setup](docs/0_ExampleSetup.md)
- [Configuration](#)
    - [Context Guard](#)
    - [Document Definition](#)
    - [Logging](#)
- [Data Creation](#)
    - [Enable automatic Update / Insert / Delete Service](#)
- [Data Fetching](#)
    - [Output Channels](docs/30_OutputChannels.md)
        - [Create Output Channel](docs/300_CreateOutputChannel.md)
        - [Multi Search Channels](#)
        - [Channel Filter / Actions](docs/302_ChannelFilterActions.md)
    - [Filter (Faceted Search / Aggregation)](#)
        - [Create Filter Definition](#)
- [API](#)

## Copyright and License
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.com)  
For licensing details please visit [LICENSE.md](LICENSE.md)

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)  
