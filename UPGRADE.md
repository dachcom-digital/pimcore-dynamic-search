# Upgrade Notes

## 4.0.7
- [BUGFIX] Reverse job processing order to prevent wrong dispatch type execution. Also merge dispatch types to improve workload 
## 4.0.6
- [LICENSE] Dual-License with GPL and Dachcom Commercial License (DCL) added
## 4.0.5
- index queue backend ui [#97](https://github.com/dachcom-digital/pimcore-dynamic-search/pull/97)
## 4.0.4
- provide ds settings in backend ui [#96](https://github.com/dachcom-digital/pimcore-dynamic-search/pull/96)
## 4.0.3
- fix null values in index [#95](https://github.com/dachcom-digital/pimcore-dynamic-search/pull/95)
## 4.0.2
- introduced backend ui event [#91](https://github.com/dachcom-digital/pimcore-dynamic-search/pull/91)
## 4.0.1
- fix resource deletion [#90](https://github.com/dachcom-digital/pimcore-dynamic-search/pull/90)

## Migrating from Version 3.x to Version 4.x

### Breaking Changes 
- The queue is now based on symfony messenger. ([#83](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/83)).   
  Execute `bin/console messenger:setup-transports`  
  read [how to setup the queue worker](docs/01_DispatchWorkflow.md#queue-worker)

***

DynamicSearch 3.x Upgrade Notes: https://github.com/dachcom-digital/pimcore-dynamic-search/blob/3.x/UPGRADE.md
