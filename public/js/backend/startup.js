class DynamicSearch {

    init() {

        var searchMenu, user = pimcore.globalmanager.get('user');

        if (!user.isAllowed('plugins')) {
            return;
        }

        Ext.Ajax.request({
            url: Routing.generate('dynamic_search.controller.admin.get_context_full_configuration'),
            success: function(response) {
                const contextFullConfig = Ext.decode(response.responseText);
                pimcore.globalmanager.add('dynamic_search.context.full_configuration', contextFullConfig);
            },
            callback: function() {
                searchMenu = new Ext.Action({
                    id: 'search',
                    text: t('dynamic_search_settings'),
                    iconCls: 'dynamic_search_bundle',
                    handler: this.openSettingsPanel.bind(this)
                });

                if (layoutToolbar.settingsMenu) {
                    layoutToolbar.settingsMenu.add(searchMenu);
                }
            }.bind(this)
        });
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
