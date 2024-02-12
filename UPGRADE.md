# Upgrade Notes

### 2.1.4
- [BUGFIX] Fix type-hint of Exceptions to match SPL Exception constructor signature [#54](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/54)

### 2.1.3
- [BUGFIX] allow empty strings being submitted [#81](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/81)

### 2.1.2
- [BACKPORT][IMPROVEMENT] Use `microtime` for envelope queue to avoid wrong processing order. Execute `bin/console dynamic-search:check-queue` before updating to this version

### 2.1.1
- [IMPROVEMENT] Added getter for `DynamicSearchBundle\Resource\ResourceCandidate::$contextName` [#55](https://github.com/dachcom-digital/pimcore-dynamic-search/pull/55)
- [IMPROVEMENT] Ignore `isAutoSave`, `saveVersionOnly` in element listener [#62](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/62)
- [BUGFIX] Remove provider cache to avoid configuration sharing [#72](https://github.com/dachcom-digital/pimcore-dynamic-search/pull/72)
- [BUGFIX] Fix parameter assignment for multi channels [#71](https://github.com/dachcom-digital/pimcore-dynamic-search/pull/71)

### 2.1.0
- [IMPROVEMENT] return raw result data if no normalizer is defined [#63](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/63)
### 2.0.2
- [BUGFIX] Fix `view_name` in default configuration `Configuration::buildContextOutputChannelsNode`
### 2.0.1
- [BUGFIX] Fix method argument definition in `DefinitionBuilderRegistry::registerFilterDefinition` [@ThisIsJustARandomGuy ](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/45)

## Migrating from Version 1.x to Version 2.0.0

### Global Changes
- Directory `var/bundles/DynamicSearchBundle` can be removed safely since it is not required anymore
- PHP8 return type declarations added: you may have to adjust your extensions accordingly
- All Folders in `views` are lowercase/dashed now (`views/common`, `views/output-channel`, ...)
- `FieldTransformerInterface::configureOptions` return type changed to `void`
- Provider bundles registration process has changed: There not automatically registered. You need to this by yourself. [Example](https://github.com/dachcom-digital/pimcore-dynamic-search-data-provider-trinity#installation).
- Paginator changed:
    - Removed Zend Paginator
    - Use Paginator from `KnpPaginatorBundle` which is included in PX by default
        - `AdapterInterface` changes (This affects you only if you're using a custom paginator `adapter_class`):
            - `count()` renamed to `getCount()`
            - `setItemCountPerPage()` added
            - `setCurrentPageNumber()` added
    - `dynamic_search_default_paginator_class` container parameter removed. If you want to modify the paginator items, just us
      the `knp_pager.items` event
    - `views/common/list/paginated/_wrapper.html.twig`, `views/common/pagination/_default.html.twig` mark-up changed, check your views accordingly
- Resource (Untrusted/Proxy) validation has been removed, you need to use the [resource validator](docs/40_ResourceValidator.md) now:
    - Methods `checkUntrustedResourceProxy` and `validateUntrustedResource` from `DataProviderInterface` has been removed. Use `DataProviderValidationAwareInterface::validateResource` instead.
    - Methods `checkUntrustedResourceProxy` and `validateUntrustedResource` from `ResourceValidatorInterface` has been removed.
    - Class `ProxyResource` has been removed.
- Logfile has been moved to symphony's default log base

### Fixes
- Improve Logger [#40](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/40)

### New Features
- Introducing backend panel and HealthState [#34](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/34)
- Provide inherited element dispatcher [#42](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/42)

***

DynamicSearch 1.x Upgrade Notes: https://github.com/dachcom-digital/pimcore-dynamic-search/blob/1.x/UPGRADE.md
