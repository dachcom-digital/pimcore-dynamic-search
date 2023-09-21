pimcore.registerNS('pimcore.plugin.dynamicSearch');

pimcore.plugin.dynamicSearch = Class.create(pimcore.plugin.admin, {

    getClassName: function () {
        return 'pimcore.plugin.dynamic_search';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    uninstall: function () {
        // void
    },

    pimcoreReady: function (params, broker) {

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

    },

    openSettingsPanel: function () {
        try {
            pimcore.globalmanager.get('dynamic_search_settings').activate();
        } catch (e) {
            pimcore.globalmanager.add('dynamic_search_settings', new pimcore.plugin.dynamicSearch.settings());
        }
    }

});

new pimcore.plugin.dynamicSearch();