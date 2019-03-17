# Pimcore 5 - Search

[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/search.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/search)
[![Travis](https://img.shields.io/travis/dachcom-digital/pimcore-search/master.svg?style=flat-square)](https://travis-ci.org/dachcom-digital/pimcore-search)
[![PhpStan](https://img.shields.io/badge/PHPStan-level%202-brightgreen.svg?style=flat-square)](#)

## Requirements
* Pimcore >= 5.4.0

## Introduction
This Search Bundle allows you to redefine your search strategy. It's based on several In- and Output Channels.

### Input Channels
- WebCrawler (Spider)
- Pimcore Data (Object, Asset, Document)

### Output Channels
- Lucene Storage
- CoreShop Index
- ElasticSearch

### Installation  

```json
"require" : {
    "dachcom-digital/search" : "~1.0.0"
}
```

### Installation via Extension Manager
After you have installed the Search Bundle via composer, open pimcore backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Enable / Disable` row
- Click the green `+` Button in `Install/Uninstall` row

## Upgrading

### Upgrading via Extension Manager
After you have updated the Search Bundle via composer, open pimcore backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Update` row

### Upgrading via CommandLine
After you have updated the Search Bundle via composer:
- Execute: `$ bin/console pimcore:bundle:update SearchBundle`

### Migrate via CommandLine
Does actually the same as the update command and preferred in CI-Workflow:
- Execute: `$ bin/console pimcore:migrations:migrate -b SearchBundle`

## Copyright and License
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)  
