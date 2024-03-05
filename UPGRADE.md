# Upgrade Notes

## Migrating from Version 3.x to Version 4.x

### Breaking Changes 
- The queue is now based on symfony messenger. ([#83](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/83)).   
  Execute `bin/console messenger:setup-transports`  
  read [how to setup the queue worker](docs/01_DispatchWorkflow.md#queue-worker)

## 3.0.1
- [BUGFIX] allow empty strings being submitted [#81](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/81)
- Use `microtime` for envelope queue to avoid wrong processing order. Execute `bin/console dynamic-search:check-queue'` before updating to this version

## Migrating from Version 2.x to Version 3.0.

### Breaking Changes
- filters will be passed to the view as associative array, having the filter names as keys (#59)  
  filter names must match the pattern `/^[a-z0-9_\-\.]+$/i`

### Global Changes
- Recommended folder structure by symfony adopted
- [ROUTE] Route include changed from `DynamicSearchBundle/Resources/config/pimcore/routing/frontend_routing.yml` to `DynamicSearchBundle/config/pimcore/routing/frontend_routing.yaml`

### Fixes
-- 

### New Features
--

***

DynamicSearch 2.x Upgrade Notes: https://github.com/dachcom-digital/pimcore-dynamic-search/blob/2.x/UPGRADE.md
