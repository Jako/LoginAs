LoginAs.util.loginAs = function () {
    var loginAs = MODx.load({
        xtype: 'loginas-window-login-as',
        title: _('loginas.login_as'),
        listeners: {
            success: {
                fn: function (r) {
                    var manager = r.f.findField('manager');
                    var result = r.a.result || {};
                    if (manager && manager.getValue()) {
                        window.location.href = (result.object && result.object.url) ? result.object.url : MODx.config.manager_url;
                    } else {
                        window.location.href = LoginAs.config.targetUrl || MODx.config.site_url || '/';
                    }
                },
                scope: this
            }
        }
    });
    loginAs.show();
}

LoginAs.window.loginAs = function (config) {
    config = config || {};
    this.ident = 'loginas-window-login-as-' + Ext.id();
    var fields = [{
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
    }];
    if (LoginAs.config.allowManager) {
        fields.push({
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('loginas.login_manager'),
            name: 'manager',
            id: this.ident + '-manager',
            inputValue: 1,
            listeners: {
                check: {
                    fn: function (checkbox, checked) {
                        var combo = Ext.getCmp(this.ident + '-username');
                        combo.getStore().setBaseParam('manager', checked ? 1 : 0);
                        combo.clearValue();
                        delete combo.lastQuery;
                    },
                    scope: this
                }
            }
        }, {
            xtype: MODx.expandHelp ? 'label' : 'hidden',
            forId: this.ident + '-manager',
            html: _('loginas.login_manager_desc'),
            cls: 'desc-under'
        });
    }
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
            handler: function () {
                this.submit();
            }
        }],
        fields: fields
    });
    LoginAs.window.loginAs.superclass.constructor.call(this, config);
};
Ext.extend(LoginAs.window.loginAs, MODx.Window, {
    submit: function (close) {
        var form = this.fp.getForm();
        var manager = form.findField('manager');
        if (!manager || !manager.getValue() || !form.isValid()) {
            return LoginAs.window.loginAs.superclass.submit.call(this, close);
        }
        // The MODX lexicon function replaces only the first occurrence of a placeholder
        var placeholders = {
            current: Ext.util.Format.htmlEncode(MODx.user.username),
            username: Ext.util.Format.htmlEncode(form.findField('username').getRawValue())
        };
        var message = _('loginas.login_as_manager_confirm');
        Ext.iterate(placeholders, function (key, value) {
            message = message.split('[[+' + key + ']]').join(value);
        });
        Ext.Msg.confirm(_('loginas.login_as_manager'), message, function (button) {
            if (button === 'yes') {
                LoginAs.window.loginAs.superclass.submit.call(this, close);
            }
        }, this);
    }
});
Ext.reg('loginas-window-login-as', LoginAs.window.loginAs);
