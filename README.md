# Pimcore - Dynamic Search

![Dynamic Search Schema](https://user-images.githubusercontent.com/700119/61217991-3c550c00-a711-11e9-9f62-6f1fb4ff0e3e.png)

[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/dynamic-search.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/dynamic-search)
[![Tests](https://img.shields.io/github/workflow/status/dachcom-digital/pimcore-dynamic-search/Codeception?style=flat-square&logo=github&label=codeception)](https://github.com/dachcom-digital/pimcore-dynamic-search/actions?query=workflow%3A%22Codeception%22)
[![PhpStan](https://img.shields.io/github/workflow/status/dachcom-digital/pimcore-dynamic-search/PHP%20Stan?style=flat-square&logo=github&label=phpstan%20level%202)](https://github.com/dachcom-digital/pimcore-dynamic-search/actions?query=workflow%3A%22PHP%20Stan%22)

## Requirements
* Pimcore >= 6.6
* Symfony >= 4.4

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
    "dachcom-digital/dynamic-search" : "~0.7.0"
}
```

## Further Information
- [Example Setup](docs/0_ExampleSetup.md)
- [Configuration](#)
    - [Context Guard](#)
    - [Document Definition](#)
    - [Logging](#)
- [Data Creation](#)
    - [Enable automatic Update / Insert / Delete Service](#)
- [Data Fetching](#)
    - [Output Channels](#)
        - [Create Output Channel](#)
        - [Multi Search Channels](#)
        - [Channel Filter / Actions](#)
    - [Filter (Faceted Search / Aggregation)](#)
        - [Create Filter Definition](#)
- [API](#)

### Installation via Extension Manager
After you have installed the Dynamic Search Bundle via composer, open pimcore backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Enable / Disable` row
- Click the green `+` Button in `Install/Uninstall` row

## Upgrading

### Upgrading via Extension Manager
After you have updated the Dynamic Search Bundle via composer, open pimcore backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Update` row

### Upgrading via CommandLine
After you have updated the Dynamic Search Bundle via composer:
- Execute: `$ bin/console pimcore:bundle:update DynamicSearchBundle`

### Migrate via CommandLine
Does actually the same as the update command and preferred in CI-Workflow:
- Execute: `$ bin/console pimcore:migrations:migrate -b DynamicSearchBundle`


## Copyright and License
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.com)  
For licensing details please visit [LICENSE.md](LICENSE.md)

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)  
