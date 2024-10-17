pimcore.registerNS('pimcore.plugin.dynamicSearch.settings');
pimcore.plugin.dynamicSearch.settings = Class.create({

    panel: null,

    initialize: function () {
        this.buildLayout();
    },

    buildLayout: function () {

        var pimcoreSystemPanel = Ext.getCmp('pimcore_panel_tabs');

        if (this.panel !== null) {
            return;
        }

        this.panel = Ext.create('Ext.panel.Panel', {
            id: 'dynamic_search_settings',
            title: t('dynamic_search_settings'),
            iconCls: 'dynamic_search_bundle',
            border: false,
            bodyPadding: 10,
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            closable: true,
            items: [
                this.buildStatusPanel(),
                this.buildProviderGrid()
            ]
        });

        this.panel.on('destroy', function () {
            pimcore.globalmanager.remove('dynamic_search_settings');
        }.bind(this));

        const postBuildLayoutEvent = new CustomEvent('dynamic_search.event.settings.postBuildLayout', {
            detail: {
                subject: this
            }
        });

        document.dispatchEvent(postBuildLayoutEvent);

        pimcoreSystemPanel.add(this.panel);
        pimcoreSystemPanel.setActiveItem('dynamic_search_settings');
    },

    buildStatusPanel: function () {

        return new Ext.panel.Table({
            title: 'Health Status',
            layout: 'table',
            viewType: 'tableview',
            style: 'margin-bottom: 10px',
            border: false,
            columnLines: true,
            stripeRows: true,
            hideHeaders: true,
            disableSelection: true,
            viewConfig: {
                trackOver: false
            },
            store: new Ext.data.JsonStore({
                autoDestroy: true,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url: Routing.generate('dynamic_search.controller.admin.get_state'),
                    reader: {
                        type: 'json',
                        rootProperty: 'lines'
                    }
                },
                fields: ['module', 'title', 'comment', 'icon']
            }),
            columns: [
                {
                    sortable: false,
                    dataIndex: 'module',
                    hidden: false,
                    flex: 1,
                },
                {
                    sortable: false,
                    dataIndex: 'title',
                    hidden: false,
                    flex: 2,
                    renderer: function (value, metaData) {
                        return '<strong>' + value + '</strong>';
                    }
                },
                {
                    sortable: false,
                    dataIndex: 'comment',
                    hidden: false,
                    flex: 4
                },
                {
                    sortable: false,
                    dataIndex: 'icon',
                    width: 100,
                    renderer: function (value, metaData) {

                        if (value === null) {
                            return '';
                        }

                        metaData.tdCls = value;

                        return '';
                    }
                }
            ]
        });
    },

    buildProviderGrid: function () {

        return new Ext.grid.GridPanel({
            title: 'Provider',
            layout: 'table',
            style: 'margin-bottom: 10px',
            columnLines: true,
            stripeRows: true,
            disableSelection: true,
            viewConfig: {
                trackOver: false
            },
            store: new Ext.data.JsonStore({
                autoDestroy: true,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url: Routing.generate('dynamic_search.controller.admin.get_provider'),
                    reader: {
                        type: 'json',
                        rootProperty: 'provider'
                    }
                },
                fields: ['id', 'path', 'active']
            }),
            columns: [

                {
                    text: t('path'),
                    sortable: false,
                    dataIndex: 'path',
                    flex: 1
                },
                {
                    text: t('active'),
                    sortable: false,
                    dataIndex: 'active',
                    width: 100,
                    renderer: function (value, metaData) {

                        if (value !== true) {
                            return null;
                        }

                        metaData.tdCls = "pimcore_icon_save";

                        return '';
                    }
                }
            ]
        });

    }
});