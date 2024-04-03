# Upgrade Notes

## Migrating from Version 3.x to Version 4.x

### Breaking Changes 
- The queue is now based on symfony messenger. ([#83](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/83)).   
  Execute `bin/console messenger:setup-transports`  
  read [how to setup the queue worker](docs/01_DispatchWorkflow.md#queue-worker)

***

DynamicSearch 3.x Upgrade Notes: https://github.com/dachcom-digital/pimcore-dynamic-search/blob/3.x/UPGRADE.md
