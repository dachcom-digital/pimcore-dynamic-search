# Upgrade Notes
![upgrade](https://user-images.githubusercontent.com/700119/31535145-3c01a264-affa-11e7-8d86-f04c33571f65.png)  

***

After every update you should check the pimcore extension manager. 
Just click the "update" button or execute the migration command to finish the bundle update.

#### Update from Version 0.x to Version 1.0.0
- **[IMPROVEMENT]**: Better exception fetching and messaging
- **[IMPROVEMENT|BC BREAK]**: Move all Configuration to Compiler [#18](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/18)
- **[IMPROVEMENT|BC BREAK]**: Implement Output Workflow Condition [#19](https://github.com/dachcom-digital/pimcore-dynamic-search/issues/19)
- **[BC BREAK]**: translation `dynamic_search.ui.we-found` and `dynamic_search.ui.items-for` has been removed
- **[BC BREAK]**: new translation keys added: `dynamic_search.ui.result_subline` (args: `%badge%` and `%query%`), `dynamic_search.ui.no_items` (args: `%count%`), `dynamic_search.ui.items` (args: `%count%`), `dynamic_search.ui.no_item` (args: `%count%`)
- **[BC BREAK]**: `SearchContainerInterface`, `MultiSearchContainerInterface` and `RawResultInterface` added. If you're using custom output channel filter, adjust these classes accordingly:
  - Method `getHitCount` removed from `OutputChannelInterface`. This needs to be defined in `RawResponse` class (available in `OutputChannelInterface::getResult(SearchContainerInterface $searchContainer)`).
  - Method `addSubQuery` removed from `MultiOutputChannelInterface`. The sub queries will be passed within `MultiOutputChannelInterface::getMultiSearchResult(MultiSearchContainerInterface $multiSearchContainer)`
  - Signature `OutputChannelInterface::getResult()` changed to `OutputChannelInterface::getResult(SearchContainerInterface $searchContainer): SearchContainerInterface`
  - Signature `MultiOutputChannelInterface::getMultiSearchResult()` changed to `MultiOutputChannelInterface::getMultiSearchResult(MultiSearchContainerInterface $multiSearchContainer): MultiSearchContainerInterface`
  - Signature `FilterInterface::findFilterValueInResult()` changed to `FilterInterface::findFilterValueInResult(RawResultInterface $rawResult)`
  - Signature `FilterInterface::buildViewVars()` changed to `FilterInterface::buildViewVars(RawResultInterface $rawResult, $filterValues, $query)`
  - Signature `DocumentNormalizerInterface::normalize()` changed to `DocumentNormalizerInterface::normalize(RawResultInterface $rawResult, ContextDefinitionInterface $contextDefinition, string $outputChannelName)`