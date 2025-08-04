LoginAs.util.loginAs = function () {
    var loginAs = MODx.load({
        xtype: 'loginas-window-login-as',
        title: _('loginas.login_as'),
        listeners: {
            success: {
                fn: function () {
                    window.location.href = LoginAs.config.targetUrl
                },
                scope: this
            },
            failure: {
                fn: function (response) {
                    MODx.msg.alert(_('error'), response.message);
                },
                scope: this
            },
        }
    });
    loginAs.show();
}

LoginAs.window.loginAs = function (config) {
    config = config || {};
    this.ident = 'loginas-window-login-as-' + Ext.id();
    Ext.applyIf(config, {
        id: this.ident,
        url: LoginAs.config.connectorUrl,
        baseParams: {
            action: 'mgr/user/loginas',
        },
        autoHeight: true,
        closeAction: 'close',
        cls: 'modx-window loginas-window' + ' modx' + LoginAs.config.modxversion,
        buttons: [{
            text: _('cancel'),
            scope: this,
            handler: this.close
        }, {
            text: _('loginas.login_as'),
            cls: 'primary-button',
            scope: this,
            handler: this.submit
        }],
        fields: [{
            style: 'margin-top: 15px',
            html: '<p>' + _('loginas.login_as_desc') + '</p>',
        }, {
            xtype: 'loginas-combo-user',
            fieldLabel: _('loginas.username'),
            name: 'username',
            id: this.ident + '-username',
            allowBlank: false,
            anchor: '100%',
            listeners: {
                afterrender: function (component) {
                    component.getEl().dom.setAttribute("data-1p-ignore", "");
                }
            }
        }]
    });
    LoginAs.window.loginAs.superclass.constructor.call(this, config);
};
Ext.extend(LoginAs.window.loginAs, MODx.Window, {});
Ext.reg('loginas-window-login-as', LoginAs.window.loginAs);
