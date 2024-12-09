pimcore.registerNS('pimcore.plugin.dynamicSearch.settings');
pimcore.plugin.dynamicSearch.settings = Class.create({

    panel: null,

    healthStateStore: null,
    providerStore: null,
    queueInfoStore: null,

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
            title: t('dynamic_search.settings'),
            iconCls: 'dynamic_search_bundle',
            border: false,
            bodyPadding: 10,
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            closable: true,
            tbar: [{
                xtype: 'button',
                iconCls: 'pimcore_icon_reload',
                handler: this.reload.bind(this)
            }],
            items: [
                this.buildStatusPanel(),
                this.buildProviderGrid(),
                this.buildQueueInfoPanel()
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

    reload: function() {
        this.queueInfoStore.reload();
        this.healthStateStore.reload();
        this.providerStore.reload();
    },

    buildQueueInfoPanel: function() {
        this.queueInfoStore = new Ext.data.JsonStore({
            autoDestroy: true,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: Routing.generate('dynamic_search.controller.admin.index_queue.get_info'),
                reader: {
                    type: 'json',
                    transform: {
                        fn: function(data) {
                            return [data];
                        }
                    }
                }
            },
            fields: ['tableName', 'count']
        });

        const contexts = Object.keys(pimcore.globalmanager.get('dynamic_search.context.full_configuration') || {});

        const performIndexQueueAction = function(action, context) {
            Ext.Msg.confirm(
                t(`dynamic_search.actions.index_queue.${action}`) + (context ? ': ' + context : ''),
                t(`dynamic_search.actions.index_queue.${action}.confirmation.message`),
                function (confirmMsg) {

                    if (confirmMsg !== 'yes') {
                        return;
                    }

                    Ext.Ajax.request({
                        url: Routing.generate('dynamic_search.controller.admin.index_queue.' + action),
                        method: 'POST',
                        params: {
                            context: context
                        },
                        success: function(response) {
                            if (response.status === 200) {
                                this.queueInfoStore.reload();
                                pimcore.helpers.showNotification(t('success'), t(`dynamic_search.actions.index_queue.${action}.success`), 'success');
                            } else {
                                pimcore.helpers.showNotification(t('error'), response.responseText, 'error');
                            }
                        }.bind(this)
                    });
                }.bind(this)
            );
        }.bind(this);

        return new Ext.grid.Panel({
            title: t('dynamic_search.settings.index_queue'),
            layout: 'table',
            hideHeaders: false,
            style: 'margin-bottom: 10px',
            store: this.queueInfoStore,
            columns: [
                {
                    text: t('dynamic_search.settings.index_queue.table_name'),
                    sortable: false,
                    dataIndex: 'tableName',
                    hidden: false,
                    flex: 2,
                },
                {
                    text: t('dynamic_search.settings.index_queue.total_queued_items'),
                    sortable: false,
                    dataIndex: 'count',
                    hidden: false,
                    flex: 1,
                    renderer: function (value, metaData) {
                        return '<strong>' + value + '</strong>';
                    }
                }
            ],
            bbar: {
                items: [
                    {
                        xtype: 'button',
                        scale: 'small',
                        margin: '0 10 0 0',
                        text: t('dynamic_search.actions.index_queue.queue_all_data'),
                        icon: '/bundles/pimcoreadmin/img/flat-color-icons/data_recovery.svg',
                        menu: contexts.map(function(context) {
                            return {
                                text: context,
                                handler: function() {
                                    performIndexQueueAction('queue_all_data', context)
                                }
                            }
                        })
                    },
                    {
                        xtype: 'button',
                        scale: 'small',
                        margin: '0 10 0 0',
                        text: t('dynamic_search.actions.index_queue.clear'),
                        icon: '/bundles/pimcoreadmin/img/flat-color-icons/delete_database.svg',
                        handler: function() {
                            performIndexQueueAction('clear', null)
                        }
                    }
                ]
            }
        });
    },

    buildStatusPanel: function () {
        this.healthStateStore = new Ext.data.JsonStore({
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
        });

        return new Ext.panel.Table({
            title: t('dynamic_search.settings.health_status'),
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
            store: this.healthStateStore,
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
        this.providerStore = new Ext.data.JsonStore({
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
        });

        return new Ext.grid.GridPanel({
            title: t('dynamic_search.settings.provider'),
            layout: 'table',
            style: 'margin-bottom: 10px',
            columnLines: true,
            stripeRows: true,
            disableSelection: true,
            viewConfig: {
                trackOver: false
            },
            store: this.providerStore,
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
