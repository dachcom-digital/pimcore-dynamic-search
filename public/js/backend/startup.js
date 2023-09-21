class DynamicSearch {

    init() {

        var searchMenu, user = pimcore.globalmanager.get('user');

        if (!user.isAllowed('plugins')) {
            return;
        }

        searchMenu = new Ext.Action({
            id: 'search',
            text: t('dynamic_search_settings'),
            iconCls: 'dynamic_search_bundle',
            handler: this.openSettingsPanel.bind(this)
        });

        if (layoutToolbar.settingsMenu) {
            layoutToolbar.settingsMenu.add(searchMenu);
        }

    }

    openSettingsPanel() {
        try {
            pimcore.globalmanager.get('dynamic_search_settings').activate();
        } catch (e) {
            pimcore.globalmanager.add('dynamic_search_settings', new pimcore.plugin.dynamicSearch.settings());
        }
    }

}

const dynamicSearchHandler = new DynamicSearch();

document.addEventListener(pimcore.events.pimcoreReady, dynamicSearchHandler.init.bind(dynamicSearchHandler));