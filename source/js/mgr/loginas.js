var loginas = function (config) {
    config = config || {};
    loginas.superclass.constructor.call(this, config);
};
Ext.extend(loginas, Ext.Component, {
    initComponent: function () {
        this.stores = {};
        this.ajax = new Ext.data.Connection({
            disableCaching: true,
        });
    }, page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, util: {}, form: {}
});
Ext.reg('loginas', loginas);

LoginAs = new loginas();

MODx.config.help_url = 'https://jako.github.io/LoginAs/usage/';
