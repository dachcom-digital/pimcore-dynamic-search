pimcore.registerNS('pimcore.plugin.search.settings');
pimcore.plugin.search.settings = Class.create({

    panel : false,
    task : null,
    loadMask : null,

    initialize: function () {
        this.getData();
    },

    getTabPanel: function () {

        var _ = this;

        this.loadMask = pimcore.globalmanager.get('loadingmask');

        if (!this.panel) {

            this.panel = Ext.create('Ext.panel.Panel', {

                id: 'search_settings',
                title: t('search_settings'),
                iconCls: 'search_icon_settings',
                border: false,
                layout: 'fit',
                closable:true

            });

            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.panel);
            tabPanel.setActiveItem('search_settings');

            this.panel.on('destroy', function () {

                pimcore.globalmanager.remove('search_settings');
                Ext.TaskManager.destroy(this.task);

            }.bind(this));

            this.container = Ext.create('Ext.Container', {

                autoScroll: true,
                scrollable: true,
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                }
            });

            this.panel.add(this.container);

            this.statusLayout = Ext.create('Ext.form.Panel', {

                id: 'LSstatusFormPanel',
                border: false,
                bodyStyle: 'background-color: #F7F7F7; padding:5px 10px;',
                autoScroll: false,

                items:[
                    {
                        xtype:'displayfield',
                        id : 'search-state-message',
                        fieldLabel: t('search_status'),
                        value: this.getCrawlerState('state')
                    },
                    {   xtype: 'buttongroup',
                        fieldLabel: t('search_frontend_crawler'),
                        hideLabel: !this.getCrawlerState('enabled'),
                        hidden: !this.getCrawlerState('enabled'),
                        columns:2,
                        bodyStyle: 'background-color: #fff;',
                        bodyBorder:false,
                        border: false,
                        frame:false
                    }
                ]
            });

            this.layout = Ext.create('Ext.form.Panel', {

                bodyStyle:'padding:20px 5px 20px 5px;',
                border: false,
                flex: 5,
                id:'search-settings-form-panel',
                autoScroll: true,
                fieldDefaults: {
                    labelWidth: 250
                },
                buttons: [],
                items: [
                    {
                        xtype:'fieldset',
                        id: 'search-log-settings',
                        title:t('search_log'),
                        collapsible: false,
                        autoHeight:true,
                        labelWidth: 100,
                        items :[
                            {
                                xtype:'textarea',
                                id: 'search-log-data',
                                collapsible: false,
                                autoHeight:false,
                                submitValue : false,
                                height:400,
                                width:'100%',
                                value:''
                            }
                        ]
                    }
                ]
            });

            this.container.add([this.statusLayout,this.layout]);

            pimcore.layout.refresh();

            this.task = Ext.TaskManager.start({
                run: this.updateCrawlerState.bind(_),
                interval: 10000
            });

            Ext.Ajax.request({
                url: '/admin/dynamic-search/settings/logs/get',
                success: function(response){
                    var data = Ext.decode(response.responseText);
                    Ext.getCmp('search-log-data').setValue(data.logData);
                }
            });

        }

        return this.panel;
    },

    updateCrawlerState : function() {
        var _ = this;
        Ext.Ajax.request({
            url: '/admin/dynamic-search/settings/get/state',
            method: 'get',
            success: function (response) {
                var res = Ext.decode(response.responseText);
                if(Ext.getCmp('search-state-message') !== undefined) {
                    Ext.getCmp('search-state-message').setValue(_.parseState(res.state));
                }
            }
        });
    },

    getData: function () {
        Ext.Ajax.request({
            url: '/admin/dynamic-search/settings/get/state',
            success: function (response) {
                this.data = Ext.decode(response.responseText);
                this.getTabPanel();
            }.bind(this)
        });
    },

    getCrawlerState: function(key) {
        var val = this.data[ key ];
        return this.parseState(val);
    },

    parseState: function(val) {
        return Ext.isArray(val) ? val.join('<br>') : val;
    }

});