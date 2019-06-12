# Pimcore 5 - Dynamic Search

[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/dynamic-search.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/dynamic-search)
[![Travis](https://img.shields.io/travis/com/dachcom-digital/pimcore-dynamic-search/master.svg?style=flat-square)](https://travis-ci.com/dachcom-digital/pimcore-dynamic-search)
[![PhpStan](https://img.shields.io/badge/PHPStan-level%202-brightgreen.svg?style=flat-square)](#)

# DO NOT USE THIS BUNDLE IN PRODUCTION


## Requirements
* Pimcore >= 5.4.0

## Introduction
The Dynamic Search Bundle allows you to redefine your search strategy. It's based on several data- and index providers.

### Data Provider
- WebCrawler (Spider)
- Pimcore Data (Object, Asset, Document)

### Index Provider
- Lucene Search
- Elastic Search

### Installation  

```json
"require" : {
    "dachcom-digital/dynamic-search" : "~1.0.0"
}
```

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
