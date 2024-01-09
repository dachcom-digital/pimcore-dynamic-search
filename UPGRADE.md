# Upgrade Notes

## 3.1.0
- [FEATURE] symfony messenger [#83](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/83). Execute `bin/console messenger:setup-transports` after update!

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
