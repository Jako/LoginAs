LoginAs.combo.User = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'username',
        valueField: 'id',
        fields: ['username', 'id'],
        pageSize: 20,
        url: LoginAs.config.connectorUrl,
        baseParams: {
            action: 'mgr/user/getlist',
            combo: true
        },
        editable: true,
        minChars: 2,
        autoSelect: false,
    });
    MODx.combo.User.superclass.constructor.call(this, config);
};
Ext.extend(LoginAs.combo.User, MODx.combo.ComboBox);
Ext.reg('loginas-combo-user', LoginAs.combo.User);
