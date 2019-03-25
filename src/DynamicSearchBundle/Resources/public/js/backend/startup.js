pimcore.registerNS('pimcore.layout.toolbar');
pimcore.registerNS('pimcore.plugin.search');

pimcore.plugin.search = Class.create(pimcore.plugin.admin, {

    getClassName: function () {
        return 'pimcore.plugin.search';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    uninstall: function () {
    },

    pimcoreReady: function (params, broker) {
        var user = pimcore.globalmanager.get('user');
        if (user.isAllowed('plugins')) {
            var searchMenu = new Ext.Action({
                id: 'search', text: t('search_settings'), iconCls: 'search_icon', handler: this.openSettings
            });

            layoutToolbar.settingsMenu.add(searchMenu);
        }
    },

    openSettings: function () {
        try {
            pimcore.globalmanager.get('search_settings').activate();
        } catch (e) {
            pimcore.globalmanager.add('search_settings', new pimcore.plugin.search.settings());
        }
    }

});

new pimcore.plugin.search();